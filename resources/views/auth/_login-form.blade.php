<form method="POST" class="auth-form" action="{{ route('login') }}">
    @csrf
    @honeypot
    
    <input type="hidden" class="hidden" name="urlHash" id="urlHash" value=""/>
    
    <div class="field">
        <label for="email" class="label">E-Mail Address</label>

        <div class="control">
            <input id="email" type="email" class="input @error('email') is-danger @enderror is-medium" 
                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="field">
        <label for="password" class="label">Password</label>

        <div class="control">
            <input id="password" type="password" class="input @error('password') is-danger @enderror is-medium" name="password" required autocomplete="current-password">

            @error('password')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>

        <p class="has-text-right mt-2">
            <a href="{{ route('password.request') }}" class="help">FORGOT PASSWORD?</a>
        </p>
    </div>

    <div class="field">
        <div class="control ">
            <label class="checkbox" for="remember">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                Remember Me
                <span class="checkbox-control"></span>
            </label>
        </div>
    </div>

    <div class="field is-grouped">
        <div class="control">
            <button type="submit" class="button is-primary-dark">Login</button>
        </div>
        @if (Route::has('password.request'))
            <div class="control">
                <a class="button is-white" href="{{ route('register') }}">
                    Sign Up
                </a>
            </div>
        @endif
    </div>
</form>