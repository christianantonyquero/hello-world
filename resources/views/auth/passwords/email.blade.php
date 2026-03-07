@extends('layouts.auth')
@section('title', 'Reset Password')

@section('main_col_content')
<div class="box">
    <div class="box-body">
        <h2 class="is-size-2 mb-4">Reset Password</h2>
        
        @if (session('status'))
            <div class="notification is-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            @honeypot
            
            <div class="field mb-5">
                <label for="email" class="label">E-Mail Address</label>

                <div class="control">
                    <input id="email" type="email" class="input @error('email') is-danger @enderror is-medium" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <p class="help is-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="field is-grouped is-grouped-multiline">
                <div class="control">
                    <button type="submit" class="button is-primary-dark">Send Password Reset Link</button>
                </div>

                <div class="control">
                    <a href="{{ route('login') }}" class="button is-outlined is-white">Back to Login</a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
