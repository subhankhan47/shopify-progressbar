$(document).ready(function () {
    const activateBtn = $('#activate-storefront-btn');
    if (activateBtn.length) {
        setTimeout(function () {
            $.ajax({
                url: '/theme-details',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    const { storeName, themeId } = data.shopifyData;
                    if (storeName && themeId) {
                        activateBtn.attr('href', `https://admin.shopify.com/store/${storeName}/themes/${themeId}/editor?context=apps`);
                        activateBtn.removeClass('d-none');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error loading Shopify theme details:", error);
                }
            });
        }, 50);
    }
});
