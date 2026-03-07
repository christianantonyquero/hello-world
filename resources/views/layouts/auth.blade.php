@extends('layouts.app')
@prepend('body_class')auth-section @endprepend

@section('content')
<section>
    <div class="auth-section-background"></div>
    <div class="container">
        <div class="columns is-multiline">
        
            <div class="column is-full-tablet is-one-third-desktop is-half-widescreen col-sidebar auth-sidebar">
                @yield('sidebar_content')
                <div class="auth-sidebar-content">
                    <img src="{{ asset('img/auth/intro-title.png') }}" alt="Lana Del Rey Village Digital Experience" />
                </div>
            </div>
        
            <div class="column col-main-content">
                @yield('main_col_content')
            </div>
        </div>
    </div>
    @include("partials._mobile-exp-message")
</section>
@endsection

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