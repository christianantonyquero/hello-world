@extends('layouts.app')
@prepend('body_class')user-profile-section @endprepend

@section('content')
<section class="user-profile">
    <div class="profile-header">
        <div class="profile-nav">
            @if (Auth::guest())
            @else
                @include('auth._logout-form')

                <a href="{{ route('home') }}" class="user-profile-close">
                    Close
                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44"><g fill="none" fill-rule="evenodd"><circle cx="22" cy="22" r="22" fill="#FFFFFF"/><path fill="#000000" fill-rule="nonzero" d="M30.067 15.564l-1.631-1.631L22 20.369l-6.436-6.436-1.631 1.631L20.369 22l-6.436 6.436 1.631 1.631L22 23.631l6.436 6.436 1.631-1.631L23.631 22z"/></g></svg>                    </a>
                </a>
            @endif
        </div>
    </div>

    <div class="profile-content">
        <div class="container is-max-desktop">
            @yield('content_user_profile')
        </div>
    </div>
    <footer class="profile-footer">
        <div class="container">
            <div class="footer-nav">
                <a href="https://lana-del-rey-village.myshopify.com/pages/privacy-and-terms-of-use" target="_blank">Privacy &amp; Terms</a>
                <a href="https://lana-del-rey-village.myshopify.com/pages/faq" target="_blank">FAQ</a>
                <a href="https://lana-del-rey-village.myshopify.com/pages/contact-us" target="_blank">Contact Us</a>
                <a href="{{route('email-us')}}" target="_blank">Feedback</a>
            </div>
            <div class="copyright">
                Copyright 2021 - Spumoni Popups
            </div>
        </div>
    </footer>
</section>
@endsection
