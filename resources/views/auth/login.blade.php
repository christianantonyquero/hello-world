@extends('layouts.auth')
@section('title', 'Login')

@section('sidebar_content')
@endsection

@section('main_col_content')
<div class="box">
    <div class="box-body">
        <h2 class="is-size-2 mb-4">Login</h2>
        @include('auth._login-form')

        <x-social-login-nav title="Or Connect With…">
        </x-social-login-nav>
       
    </div>
</div>
@endsection

@push('extra_js')
    <script>
        // Try and all login redirect to work with URL hash fragments used by SPAs
        document.getElementById('urlHash').value = window.location.hash;
    </script>
@endpush
