@extends('layouts.app')
@section('title', $store->name)

@section('body')
    <div class="" id="app"></div>
    @include("partials._mobile-exp-message")
@endsection

@push('body_class')store-app store-{{ $store->slug }}@endpush

@push('extra_js')
    <script>
        window.__INIT_STATE__ = {
            store: {!! json_encode($store) !!},
            user: {!! json_encode($user) !!},
            SHOPIFY_STORE_DOMAIN: "{{ config('services.shopify.store_domain') }}",
            SHOPIFY_STOREFRONT_ACCESS_TOKEN: "{{ config('services.shopify.storefront_access_token') }}",
        }
    </script>
    <script src="{{ mix('js/store/store_app.js') }}" crossorigin="anonymous"></script>
@endpush

@push('extra_js')
    <script>
        // toggle code for mobile message
        var mobileMsg = document.getElementById('mobile-exp-message');
        var btnMobileMsgClose = document.getElementById('btn-mobile-msg-close');

        if(mobileMsg && btnMobileMsgClose ) {
            const hasShownMobileExpMsg = localStorage.getItem('hasShownMobileExpMsg');
            if( !hasShownMobileExpMsg ) {
                mobileMsg.classList.remove('hide');
                // We've shown the message, now track it so it isn't shown again
                localStorage.setItem('hasShownMobileExpMsg', true);
            }

            btnMobileMsgClose.addEventListener('click', function() {
                console.log('close message click');
                // Hide message and log that the message has been hidden
                mobileMsg.classList.add('hide');
                // track we've shown it on dismiss just to be safe
                localStorage.setItem('hasShownMobileExpMsg', true);
            });
        }
    </script>
@endpush
