<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="shopify-api-key"
        content="{{ \Osiset\ShopifyApp\Util::getShopifyConfig('api_key', $shopDomain ?? Auth::user()->name) }}" />
    <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>
    <script src="https://unpkg.com/@shopify/app-bridge@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-minicolors/2.3.6/jquery.minicolors.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-minicolors/2.3.6/jquery.minicolors.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <title>{{ config('shopify-app.app_name') }}</title>
    @yield('styles')
</head>

<body>
    <div class="app-wrapper">
        <div class="app-content">
            <main role="main">
                @yield('content')
                @include('partials.videoGuide')
            </main>
        </div>
    </div>

    <script>
        var AppBridge = window['app-bridge'];

        var actions = AppBridge.actions;
        var utils = window['app-bridge-utils'];
        var NavigationMenu = actions.NavigationMenu;
        var AppLink = actions.AppLink;
        var createApp = AppBridge.default;
        var app = createApp({
            apiKey: "{{ \Osiset\ShopifyApp\Util::getShopifyConfig('api_key', base64_decode(\Request::get('host'))) }}",
            shopOrigin: "{{ base64_decode(\Request::get('host')) }}",
            host: "{{ \Request::get('host') }}",
            forceRedirect: true,
        });

        var linksArray = [];
        const thresholdLink = AppLink.create(app, {
            label: 'Threshold',
            destination: '/thresholds',
        });
        const plansLink = AppLink.create(app, {
            label: 'Plans',
            destination: '/plans',
        });
        linksArray.push(thresholdLink,plansLink);

        const navigationMenu = NavigationMenu.create(app, {
            items: linksArray,
            active: undefined,
        });
        const redirect = actions.Redirect.create(app);
        const navigation = function(url) {
            redirect.dispatch(actions.Redirect.Action.APP, url);
        };

        function showToast(app, message, isError = false, duration = 3000) {
            var Toast = actions.Toast;
            const toast = Toast.create(app, {
                message: message,
                duration: duration,
                isError: isError,
            });
            toast.dispatch(Toast.Action.SHOW);
        }
    </script>

    @if (\Osiset\ShopifyApp\Util::useNativeAppBridge())
        @include('shopify-app::partials.token_handler')
    @endif
    @yield('scripts')
</body>

</html>
