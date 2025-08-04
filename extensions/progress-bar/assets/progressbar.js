const shop = Shopify.shop;
const baseURL = 'https://sf-rewardbar.sfaddons.com/';
const lineItemSelectors = 'tr, .cart__row, .cart-item, .cart-item-row, .line-item';
const quantityButtonSelectors = 'quantity-input, input.quantity, input[name^="updates"], .quantity__input, .js-qty__input';
const radioSelectors = 'label,input[type="radio"]';
(function ($) {
    $(document).ready(function () {
        removeFreeRadioVariant();
        initProgressBar();
        watchCartChanges();
    });
    let isAddingReward = false;
    let lastCartToken = null;
    async function initProgressBar() {
        if (!shop) return;
        disableRewardProductQuantityControlsOnCartPage();

        try {
            const [settings, cart] = await Promise.all([
                fetchSettings(shop),
                fetchCart()
            ]);

            if (!settings || !cart) return;
            renderProgressBar(settings, cart);
        } catch (err) {
            console.error('Progress bar error:', err);
        }
    }

    async function renderProgressBar(settings, cart) {
        const setting = settings.settings || {};
        const cartTotal = cart?.items_subtotal_price ? cart.items_subtotal_price / 100 : 0;

        // Page condition check
        const isHome = location.pathname === '/';
        const isCollection = location.pathname.includes('/collections/');
        const isProduct = location.pathname.includes('/products/');
        const showOnCurrentPage = (
            (isHome && setting.home_page_show) ||
            (isCollection && setting.collection_page_show) ||
            (isProduct && setting.product_page_show)
        );

        if (!showOnCurrentPage) return;

        const threshold = resolveThreshold(cartTotal, settings.thresholds);
        if (!threshold) return;

        const progress = calculateProgress(cartTotal, threshold.amount);
        const message = buildRewardMessage(threshold, cartTotal, setting);
        let rewardVariantId = localStorage.getItem('rewardVariantId');
        const thresholdMet = cartTotal >= threshold.amount;

        // Auto-add reward product if required, Auto-remove if total drops
        if (threshold.reward_type === 'free_product') {
            if (thresholdMet && !rewardVariantId && !isAddingReward) {
                isAddingReward = true;
                rewardVariantId = await createRewardVariantIfNeeded(threshold.product_id);
                if (rewardVariantId) localStorage.setItem('rewardVariantId', rewardVariantId);
                isAddingReward = false;
            }

            if (rewardVariantId) {
                const alreadyInCart = isProductAlreadyInCart(cart, rewardVariantId);

                if (thresholdMet) {
                    if (threshold.auto_add_product && !alreadyInCart) {
                        await autoAddProduct(rewardVariantId);
                        const updatedCart = await fetchCart();
                        return renderProgressBar(settings, updatedCart);
                    }
                } else {
                    if (alreadyInCart) {
                        await removeProductFromCart(rewardVariantId);
                        localStorage.removeItem('rewardVariantId');
                        const updatedCart = await fetchCart();
                        return renderProgressBar(settings, updatedCart);
                    }
                }
            }
        }


        $('#progressbar-top').remove();
        $('#progressbar-widget').remove();
        $('#progressbar-drawer').remove();
        // Inject UI based on flags
        if (setting.top_bar_enabled) {
            injectInlineProgressBar(progress, message, settings.progressbar_style, setting, cart.items);
        }

        if (setting.sticky_widget_enabled) {
            injectWidgetProgressBar(progress, message, settings, cart.items);
        }
        if (threshold.reward_type === 'free_product' && thresholdMet && !threshold.auto_add_product && rewardVariantId) {
            const alreadyInCart = isProductAlreadyInCart(cart, rewardVariantId);
            if (!alreadyInCart) {
                injectClaimGiftCTA(rewardVariantId);
            }
        }
    }

    function fetchSettings(shop) {
        return $.ajax({
            url: `${baseURL}storefront/settings?shop=${shop}`,
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-Shopify-Shop-Domain": shop,
            },
        }).then(res => res).catch(() => null);
    }

    function fetchCart() {
        return $.ajax({
            url: '/cart.js',
            method: 'GET',
            dataType: 'json'
        }).then(res => {
            return {
                ...res,
                items: Array.isArray(res.items) ? res.items : [],
            };
        }).catch(() => {
            return {
                items: [],
            };
        });
    }

    function resolveThreshold(cartTotal, thresholds = []) {
        if (!thresholds.length) return null;
        const sorted = thresholds
            .sort((a, b) => {
                if (a.priority !== b.priority) return a.priority - b.priority;
                return a.amount - b.amount;
            });

        let bestThreshold = null;
        for (const threshold of sorted) {
            if (cartTotal >= threshold.amount) {
                bestThreshold = threshold;
            }
        }
        return bestThreshold || sorted[0];
    }

    function buildRewardMessage(threshold, cartTotal, setting) {
        const remaining = threshold.amount - cartTotal;
        if (remaining > 0) {
            const base = setting.custom_message || 'Spend more to unlock';
            return `${base}: $${remaining.toFixed(2)} to unlock ${formatReward(threshold)}`;
        }
        const unlocked = setting.completion_message || 'Reward unlocked';
        return `${unlocked}: ${formatReward(threshold)}`;
    }

    function formatReward(threshold) {
        if (threshold.reward_type === 'free_shipping') return 'Free Shipping';
        if (threshold.reward_type === 'free_product') return 'Free Gift';
        return 'Reward';
    }

    function calculateProgress(cartTotal, goalAmount) {
        return Math.min((cartTotal / goalAmount) * 100, 100);
    }

    function injectInlineProgressBar(progress, message, style, setting, cartItems = []) {
        $('#progressbar-top').remove();
        style = style || {};
        setting = setting || {};
        const {
            bg_color = '#f1f1f1',
            font_color = '#000000',
            font_size = 14,
            border_radius = 5,
            filled_progress_color = '#4caf50',
            message_position = 'bottom',
            show_products_in_bar = true,
        } = style;

        const animation = setting.animation_style || 'fade-in';

        const variables = `
        --pb-bg-color: ${bg_color};
        --pb-font-color: ${font_color};
        --pb-font-size: ${font_size}px;
        --pb-border-radius: ${border_radius}px;
        --pb-filled-progress-color: ${filled_progress_color};
        --pb-animation: ${animation} 0.5s ease;
    `;
        let productsHtml = '';
        if (show_products_in_bar && Array.isArray(cartItems) && cartItems.length > 0) {
            productsHtml = `
              <div class="progressbar-carousel-wrapper">
                <button class="carousel-arrow left">
                  <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 18l-6-6 6-6"/>
                  </svg>
                </button>

                <div class="progressbar-products-container">
                  <div class="progressbar-products">
                    ${cartItems.map(item => {
                const totalPrice = ((item.price * item.quantity) / 100).toFixed(2);
                const displayImage = item.image && item.image.startsWith('http') ? item.image : 'https://t4.ftcdn.net/jpg/04/23/98/19/240_F_423981991_w1ZYf0ah4WWKe1R8BOxd3OgGDRPKEzp1.jpg';
                const pTitle = item.title? item.title : 'Free Gift';
                return `
                        <div class="pb-product">
                          <img src="${displayImage}" alt="${pTitle}" class="pb-product-image" />
                          <div class="pb-product-title">${pTitle}</div>
                          <div class="pb-product-meta">
                            <span>Qty: ${item.quantity}</span>
                            <span>$${totalPrice}</span>
                          </div>
                        </div>
                      `;
            }).join('')}
                  </div>
                </div>

                <button class="carousel-arrow right">
                  <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 6l6 6-6 6"/>
                  </svg>
                </button>
              </div>
            `;
        }

        const $container = $(`
        <div id="progressbar-top" style="${variables}">
            ${message_position === 'top' ? `<div>${message}</div>` : ''}
            ${productsHtml}
            <div class="progress-bar-wrapper">
                <div class="progress-bar-fill" style="width: ${progress}%;"></div>
            </div>
            ${message_position === 'bottom' ? `<div>${message}</div>` : ''}
        </div>
    `);

        $('body').prepend($container);
        const $products = $('.progressbar-products');
        $('.carousel-arrow.left').on('click', () => {
            $products.animate({ scrollLeft: '-=150' }, 200);
        });
        $('.carousel-arrow.right').on('click', () => {
            $products.animate({ scrollLeft: '+=150' }, 200);
        });
    }

    function injectWidgetProgressBar(progress, message, settings, cartItems = []) {
        $('#progressbar-widget').remove();
        const style = settings.widget_style || {};

        const isCircle = style.widget_shape === 'rounded';
        const width = style.width || 60;
        const height = style.height || 60;
        const radius = 26;
        const stroke = 4;
        const normalizedRadius = radius - stroke * 0.5;
        const circumference = 2 * Math.PI * normalizedRadius;
        const offset = circumference - (progress / 100) * circumference;

        let positionTop = '';
        let positionBottom = '';
        let positionLeft = '';
        let positionRight = '';

        if (style.position === 'center-left') {
            positionLeft = 'left: 20px;';
            positionTop = 'top: 50%; transform: translateY(-50%);';
        } else if (style.position === 'center-right') {
            positionRight = 'right: 20px;';
            positionTop = 'top: 50%; transform: translateY(-50%);';
        } else if (style.position === 'bottom-left') {
            positionLeft = 'left: 20px;';
            positionBottom = 'bottom: 20px;';
        } else if (style.position === 'bottom-right') {
            positionRight = 'right: 20px;';
            positionBottom = 'bottom: 20px;';
        } else {
            positionRight = 'right: 20px;';
            positionTop = 'top: 50%; transform: translateY(-50%);';
        }

        const wrapperStyles = `
        background-color: ${style.bg_color || '#fff'};
        width: ${width}px;
        height: ${height}px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: ${isCircle ? '50%' : '8px'};
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    `;

        const containerStyles = `
        position: fixed;
        ${positionTop}
        ${positionBottom}
        ${positionLeft}
        ${positionRight}
        z-index: 9999;
        cursor: ${style.open_drawer ? 'pointer' : 'default'};
    `;

        const svgHtml = `
        <svg width="${width}" height="${height}" viewBox="0 0 ${width} ${height}">
            <circle
                cx="${width / 2}"
                cy="${height / 2}"
                r="${normalizedRadius}"
                stroke="${style.bg_track_color || '#ddd'}"
                stroke-width="${stroke}"
                fill="none"
            />
            <circle
                cx="${width / 2}"
                cy="${height / 2}"
                r="${normalizedRadius}"
                stroke="${style.filled_progress_color || '#4caf50'}"
                stroke-width="${stroke}"
                fill="none"
                stroke-dasharray="${circumference}"
                stroke-dashoffset="${offset}"
                stroke-linecap="round"
                transform="rotate(-90 ${width / 2} ${height / 2})"
            />
            <text
                x="50%"
                y="50%"
                dominant-baseline="middle"
                text-anchor="middle"
                font-size="10"
                fill="${style.font_color || '#000'}"
            >
                ${Math.floor(progress)}%
            </text>
        </svg>
    `;

        const $widget = $(`
        <div id="progressbar-widget" style="${containerStyles}">
            <div class="progressbar-wrapper" style="${wrapperStyles}">
                ${svgHtml}
            </div>
        </div>
    `);

        if (style.open_drawer) {
            $widget.on('click', function () {
                injectProgressDrawer(progress, message, settings.drawer_style, cartItems);
            });
        }

        $('body').append($widget);
    }

    function injectProgressDrawer(progress, message, style, cartItems = []) {
        $('#progressbar-drawer').remove();

        style = style || {};
        const {
            bg_color = '#ffffff',
            font_color = '#000000',
            font_size = 14,
            filled_progress_color = '#4caf50',
            animation = 'slide',
            layout = 'vertical',
            show_products_in_bar = true,
            message_position = 'bottom'
        } = style;

        const isVertical = layout === 'vertical';
        const showProducts = show_products_in_bar;

        const variables = `
        --drawer-bg-color: ${bg_color};
        --drawer-font-color: ${font_color};
        --drawer-font-size: ${font_size}px;
        --drawer-filled-progress-color: ${filled_progress_color};
        --drawer-animation: ${animation} 0.5s ease;
    `;

        let productsHtml = '';
        if (showProducts && Array.isArray(cartItems) && cartItems.length > 0) {
            productsHtml = `
            <div class="progressbar-carousel-wrapper">
                <button class="carousel-arrow left" aria-label="Scroll left">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </button>

                <div class="progressbar-products-container">
                    <div class="progressbar-products">
                        ${cartItems.map(item => {
                const totalPrice =  ((item.price * item.quantity) / 100).toFixed(2);
                const displayImage = item.image && item.image.startsWith('http') ? item.image : 'https://t4.ftcdn.net/jpg/04/23/98/19/240_F_423981991_w1ZYf0ah4WWKe1R8BOxd3OgGDRPKEzp1.jpg';
                const pTitle = item.title? item.title : 'Free Gift';
                return `
                                <div class="pb-product">
                                    <img src="${displayImage}" alt="${pTitle}" class="pb-product-image" />
                                    <div class="pb-product-title">${pTitle}</div>
                                    <div class="pb-product-meta">
                                        <span>Qty: ${item.quantity}</span>
                                        <span>$${totalPrice}</span>
                                    </div>
                                </div>
                            `;
            }).join('')}
                    </div>
                </div>

                <button class="carousel-arrow right" aria-label="Scroll right">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 6l6 6-6 6"/>
                    </svg>
                </button>
            </div>
        `;
        }
        const drawerClass = isVertical ? 'vertical-drawer' : 'horizontal-drawer';
        const barWrapper = `
        <div class="progress-bar-wrapper">
            <div class="progress-bar-fill" style="width: ${progress}%;"></div>
        </div>
    `;
        const $drawer = $(`
        <div id="progressbar-drawer" class="${drawerClass}" style="${variables}">
            ${message_position === 'top' ? `<div class="drawer-message">${message}</div>` : ''}
            ${barWrapper}
            ${productsHtml}
            ${message_position === 'bottom' ? `<div class="drawer-message">${message}</div>` : ''}
            <button id="close-drawer" class="drawer-close-btn" aria-label="Close progress drawer">Close</button>
        </div>
    `);

        $('body').append($drawer);
        $drawer.css('transform', 'translateY(100%)');
        setTimeout(() => {
            $drawer.css('transform', 'translateY(0)');
        }, 10);

        $drawer.find('#close-drawer').on('click', function () {
            $drawer.css('transform', 'translateY(100%)');
            setTimeout(() => $drawer.remove(), 500);
        });
        const $products = $drawer.find('.progressbar-products');
        $drawer.find('.carousel-arrow.left').on('click', () => {
            $products.animate({ scrollLeft: '-=150' }, 200);
        });
        $drawer.find('.carousel-arrow.right').on('click', () => {
            $products.animate({ scrollLeft: '+=150' }, 200);
        });
    }

    function isProductAlreadyInCart(cart, variantId) {
        return cart.items.some(item => item.variant_id == variantId);
    }

    function autoAddProduct(variantId) {
        return $.ajax({
            url: '/cart/add.js',
            method: 'POST',
            dataType: 'json',
            data: {
                id: variantId,
                quantity: 1
            }
        }).then(res => {
            console.log('Free product added:', res);
            return res;
        }).catch(err => {
            console.error('Error auto-adding product:', err);
        });
    }
    function removeProductFromCart(variantId) {
        return fetchCart().then(cart => {
            const item = cart.items.find(i => i.variant_id == variantId);
            if (!item) return;
            return $.ajax({
                url: '/cart/change.js',
                method: 'POST',
                data: {
                    id: item.key,
                    quantity: 0,
                },
                dataType: 'json'
            });
        });
    }

    function createRewardVariantIfNeeded(sourceVariantId) {
        return $.ajax({
            url: `${baseURL}storefront/create-reward-variant`,
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Shopify-Shop-Domain': Shopify.shop,
            },
            data: JSON.stringify({
                shop: Shopify.shop,
                source_variant_id: sourceVariantId
            }),
            dataType: 'json'
        }).then(res => {
            if (res && res.product_handle) {
                localStorage.setItem('rewardVariantProductHandle', res.product_handle);
            }
            if (res && res.variant_id) {
                return res.variant_id;
            }
            return null;
        }).catch(err => {
            console.error('Failed to create/retrieve reward variant:', err);
            return null;
        });
    }



    // ── Cart Watcher ──────────────────────────────────────
    function watchCartChanges() {
        let debounceTimer = null;
        const pollCart = () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(async () => {
                const cart = await fetchCart();
                const token = cart.items.map(i => `${i.id}:${i.quantity}`).join('|');
                if (token !== lastCartToken) {
                    lastCartToken = token;
                    initProgressBar();
                }
            }, 1000); // debounce 1s
        };

        const mo = new MutationObserver(pollCart);
        mo.observe(document.body, { childList: true, subtree: true });
        $(document).on('ajaxCart:afterCartLoad', pollCart);
        setInterval(pollCart, 15000); // fallback poll every 15s
    }

    async function disableRewardProductQuantityControlsOnCartPage() {
        if (!window.location.pathname.startsWith('/cart')) return;

        const rewardVariantId = localStorage.getItem('rewardVariantId');
        if (!rewardVariantId) return;

        try {
            const [settings, cart] = await Promise.all([
                fetchSettings(shop),
                fetchCart()
            ]);

            const cartTotal = cart?.items_subtotal_price ? cart.items_subtotal_price / 100 : 0;
            const thresholds = settings?.thresholds || [];

            const activeThreshold = resolveThreshold(cartTotal, thresholds);

            const rewardItem = cart.items.find(item => item.variant_id == rewardVariantId);

            const thresholdMet =
                activeThreshold?.reward_type === 'free_product' &&
                cartTotal >= activeThreshold.amount &&
                rewardItem;

            if (!thresholdMet && rewardItem) {
                await removeProductFromCart(rewardVariantId);
                localStorage.removeItem('rewardVariantId');
                location.reload();
                return;
            }
            lockRewardQuantityControls(rewardVariantId);
        } catch (err) {
            console.error('Failed to validate reward on cart page:', err);
        }
    }
    function removeRewardLineItemFromDOM(variantId) {
        $('a').each(function () {
            const href = $(this).attr('href') || '';
            if (href.includes(variantId)) {
                const $line = $(this).closest(lineItemSelectors);
                $line.fadeOut(300, () => $line.remove());
            }
        });
    }
    function lockRewardQuantityControls(variantId) {
        $('a').each(function () {
            const href = $(this).attr('href') || '';
            if (href.includes(variantId)) {
                const $line = $(this).closest(lineItemSelectors);
                $line.find(quantityButtonSelectors).remove();
            }
        });
    }

    function removeFreeRadioVariant() {
        const searchTerm = '- free';
        $('body').children().not('.progressbar-products').find('*').each(function () {
            const text = $(this).text().trim().toLowerCase();
            if (text.includes(searchTerm)) {
                $(this).closest(radioSelectors).remove();
            }
        });

    }

    function injectClaimGiftCTA(rewardVariantId) {
        if ($('#pb-claim-gift').length) return;
        const $btn = $(`
      <button id="pb-claim-gift"
              style="
                  margin-top:8px;
                  padding:10px 16px;
                  background:#4caf50;
                  color:#fff;
                  border:none;
                  border-radius:4px;
                  cursor:pointer;">
          🎁 Claim your free gift
      </button>
    `).on('click', () => {
            window.location.href = `/cart?claim-reward=1&variant_id=${rewardVariantId}`;
        });
        $('#progressbar-top').append($btn.clone(true));
        $('#close-drawer').before($btn.clone(true));
    }

    const urlParams = new URLSearchParams(window.location.search);
    const isClaimPage = urlParams.has('claim-reward') && urlParams.has('variant_id');
    if (isClaimPage) {
        const rewardVariantId = urlParams.get('variant_id');
        showRewardClaimPage(rewardVariantId);
    }

    async function showRewardClaimPage(variantId) {
        const productHandle = localStorage.getItem('rewardVariantProductHandle');
        $('body').empty().css({
            padding: '20px',
            fontFamily: 'Arial, sans-serif',
            textAlign: 'center',
        }).append('<h2>🎁 Claim your Free Gift</h2>');

        // Step 1: Fetch cart and settings
        const [cart, settings] = await Promise.all([
            fetchCart(),
            fetchSettings(shop)
        ]);

        const thresholds = settings?.thresholds || [];
        const cartTotal = cart?.items_subtotal_price ? cart.items_subtotal_price / 100 : 0;
        const activeThreshold = resolveThreshold(cartTotal, thresholds);
        const isRewardThresholdMet =
            activeThreshold?.reward_type === 'free_product' &&
            cartTotal >= activeThreshold.amount;

        const alreadyInCart = isProductAlreadyInCart(cart, variantId);

        if (!isRewardThresholdMet) {
            $('<p>').text('This reward is no longer available. Please meet the minimum spend to unlock it again.').css('color', 'red').appendTo('body');
            return;
        }

        // Step 3: Show gift image and title
        try {
            const data = await fetch(`/variants/${variantId}.js`).then(res => res.json());
            const prodData = await fetch(`/products/${productHandle}.js`).then(res => res.json());
            $('<img>')
                .attr('src', prodData.featured_image.startsWith('http') ? prodData.featured_image : `https:${prodData.featured_image}`)
                .css({ maxWidth: '200px', margin: 'auto', borderRadius: '8px' })
                .appendTo('body');
            $('<h3>')
                .text(data.name.toLowerCase().includes('default title') ? prodData.title : data.name)
                .css({ fontSize: '20px', marginBottom: '10px' })
                .appendTo('body');
        } catch {
            $('<p>').text('Unable to load reward details.').css('color', 'red').appendTo('body');
            return;
        }

        if (alreadyInCart) {
            $('<p>').text('You’ve already claimed this reward. Check your cart!').css('color', '#4caf50').appendTo('body');
            return;
        }

        $('<p>')
            .text('You’ve unlocked a reward! Click the button below to add your free gift to the cart.')
            .appendTo('body');

        const $btn = $('<button>')
            .text('Add Gift to Cart')
            .css({
                padding: '10px 5px',
                backgroundColor: '#4caf50',
                color: '#fff',
                border: 'none',
                borderRadius: '6px',
                cursor: 'pointer',
                fontSize: '16px',
                marginTop: '10px'
            })
            .on('click', async () => {
                $btn.prop('disabled', true).text('Adding...');
                await autoAddProduct(variantId);
                localStorage.setItem('rewardVariantId', variantId);
                window.location.href = '/cart';
            });

        $('body').append($btn);
    }


})(jQuery);
