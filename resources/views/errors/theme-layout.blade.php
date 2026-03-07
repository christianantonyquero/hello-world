@extends('layouts.app')
@prepend('body_class')error-section auth-section @endprepend

@section('body')
<div class="auth-section-background"></div>
<div class="container">
    <div class="columns is-multiline">
        <div class="column col-main-content">
            <div class="box">
                <div class="box-body">
                    {{--
                    <h2 class="error-code is-size-2 mb-4">
                        @yield('code')
                    </h2>
                    --}}
                    <h2 class="error-message is-size-2 mb-4">
                        @yield('message')
                    </h2>
                </div>
        </div>
</div>
@endsection