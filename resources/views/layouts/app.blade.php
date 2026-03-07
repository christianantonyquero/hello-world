<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    @includeWhen(config('services.analytics.gtag_id'), 'partials._google-analyitcs', 
        ['gtag_id' => config('services.analytics.gtag_id'),
         'app_name' => config('app.name')])

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WBFMDXR');</script>
    <!-- End Google Tag Manager -->

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="google-site-verification" content="yb_Pz9RoeaDvZzYy-WqFYCpp7KQrRT0exHjMTl4hkEg"/>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png')}}">

    @section('sharing_meta')
        <meta property="og:url" content="{{ config('app.url') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="@yield('title') - {{ config('app.name') }}" />
        <meta property="og:description" content="{{ config('app.name') }}" />
        <meta property="og:image" content="{{ asset('img/social-share.jpg') }}" />
    @show

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name') }}</title>
    
    <script defer src="{{ asset('vendor-css/modernizr-custom.js')}}"></script>

    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <link href="{{ mix('css/store/store-app.css') }}" rel="stylesheet" />

    @stack('extra_head')
</head>
<body id="@yield('body_id')" class="@stack('body_class')">

    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WBFMDXR" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->

    @section('body')
        <main class="main">
            @include('flash::message')
            @yield('content')
        </main>
    @show
    @stack('extra_js')
</body>
</html>
