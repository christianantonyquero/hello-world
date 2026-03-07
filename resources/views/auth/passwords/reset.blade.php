@extends('layouts.auth')
@section('title', 'Reset Password')

@section('main_col_content')

<div class="box">
    <h4 class="is-size-4 mb-4">Reset Password</h4>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
            <label for="email" class="label">E-Mail Address</label>

            <div class="control">
                <input id="email" type="email" class="input @error('email') is-danger @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="field">
            <label for="password" class="label">Password</label>

            <div class="control">
                <input id="password" type="password" class="input @error('password') is-danger @enderror" name="password" required autocomplete="new-password">

                @error('password')
                    <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="field">
            <label for="password-confirm" class="label">Confirm Password</label>

            <div class="control">
                <input id="password-confirm" type="password" class="input" name="password_confirmation" required autocomplete="new-password">
            </div>
        </div>

        <div class="field">
            <div class="control">
                <button type="submit" class="button is-primary">Reset Password</button>
            </div>
        </div>
    </form>
</div>

@endsection
