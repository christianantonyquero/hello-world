@extends('layouts.auth')
@section('title', 'Register')

@section('sidebar_content')
@endsection

@section('main_col_content')
<div class="box">
    <div class="box-body">
        <h2 class="is-size-2 mb-4">Register</h2>
        @if($social_registration)
            <div class="alert alert-social-register">
                We're creating your account using <span class="provider-name">{{$social_registration['provider_name']}}</span> account and we need a little more information&hellip;
            </div>
        @endif

        @include('auth._register-form')

        <x-social-login-nav title="Or Connect With…">
        </x-social-login-nav>
    </div>
</div>
@endsection
