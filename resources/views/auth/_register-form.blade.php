<form method="POST" class="auth-form" action="{{ route('register') }}">
    @csrf
    @honeypot
    
    <div class="columns is-variable is-2 is-multiline is-mobile">
        <div class="column is-full-mobile is-full-desktop is-half-widescreen control">
            <label class="label" for="email">E-Mail Address</label>
            @if(isset($social_registration['email']))
                <span class="social-value">{{$social_registration['email']}}</span>
                <input id="email" name="email" type="hidden" value="{{ $social_registration['email'] }}" />
            @else
                <input id="email" type="email" class="input @error('email') is-danger @enderror" name="email" 
                        value="{{ old('email', optional($social_registration)['email']) }}" 
                        required autocomplete="email" autofocus>
            @endif
            @error('email')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
    
        <div class="column is-full-mobile is-full-desktop is-half-widescreen control">
            <label class="label" for="name">Name</label>
            @if(isset($social_registration['name']))
                <span class="social-value">{{$social_registration['name']}}</span>
                <input id="name" name="name" type="hidden" value="{{ $social_registration['name'] }}" />
            @else 
                <input id="name" type="text" class="input @error('name') is-danger @enderror" name="name"
                    value="{{ old('name', optional($social_registration)['name']) }}" required autocomplete="name">
            @endif
            @error('name')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="columns is-variable is-2 is-multiline is-mobile">
        <div class="column is-full-mobile is-full-desktop is-half-widescreen control">
            <label class="label" for="password">Password</label>
            <input id="password" type="password" 
                   class="input @error('password') is-danger @enderror" name="password" required autocomplete="new-password">
            @include('partials._password-rules')
            @error('password')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="column is-full-mobile is-full-desktop is-half-widescreen control">
            <label class="label" for="password-confirm">Confirm Password</label>
            <input id="password-confirm" type="password" class="input" name="password_confirmation" required autocomplete="new-password">
        </div>
    </div>

    {{-- LDR Rep asked to hide DOB and Phone for now 
    <div class="columns is-variable is-2 field">
        <div class="control column">
            <label class="label" for="date_of_birth">Date of Birth</label>
            <input name="date_of_birth" id="date_of_birth" type="date"
                required
                class="input @error('date_of_birth') is-invalid @enderror"
                value="{{ old('date_of_birth') }}" />
    
            @error('date_of_birth')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="control column">
            <label class="label" for="phone">Phone Number</label>
            <input name="phone" id="phone" type="tel" placeholder="555-555-1212"
                class="input @error('phone') is-invalid @enderror" 
                value="{{ old('phone') }}" />
    
            @error('phone')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
    --}}
    {{-- country --}}
    <div class="columns">
        <div class="column">
            <label class="label" for="country">Country</label>
            <div class="select @error('country') is-invalid @enderror">
                <select id="country" name="country" required>
                    <option value="" >-- Select a Country --</option>
                    @foreach($countries as $code => $name)
                        <option value="{{$code}}"
                            {{ old('country') == $code ? 'selected' : ''}}>{{$name}}</option>
                    @endforeach
                </select>
            </div>
            @error('country')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
    

    <div class="field mt-4 mb-5">
    {{--- umusic opt-in fields --}}
    {{--
   
        <div class="control">
            <label class="checkbox" for="umusic_email_opt_in">
                <input id="umusic_email_opt_in" name="umusic_email_opt_in" 
                    {{ old('umusic_email_opt_in') ? 'checked' : ''}}
                    type="checkbox" value="1" />
                Sign up for emails from Lana Del Rey: news, exclusives and more, sent by Universal Music.
                <a href="https://umusic.co.uk/privacy.html" target="_blank">Privacy Policy</a>
                <span class="checkbox-control"></span>
            </label>
            @error('umusic_email_opt_in')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
        <div class="control">
            <label class="checkbox" for="umusic_sms_opt_in" class="form-check-label">
                <input id="umusic_sms_opt_in" name="umusic_sms_opt_in" 
                    {{ old('umusic_sms_opt_in') ? 'checked' : ''}} type="checkbox" value="1"
                    class="@error('umusic_sms_opt_in') is-danger @enderror" />
                Sign up for SMS updates from Lana Del Rey, sent by Universal Music.
                <a href="https://umusic.co.uk/privacy.html" target="_blank">Privacy Policy</a>
                <span class="checkbox-control"></span>
            </label>
            @error('umusic_sms_opt_in')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
        --}}
        
   {{-- end umusic opt-in fields --}}

        <div class="control">
            <label class="checkbox" for="agree_to_terms" class="form-check-label">
                <input id="agree_to_terms" 
                    name="agree_to_terms"
                    {{ old('agree_to_terms') ? 'checked' : ''}}
                    type="checkbox" class="@error('agree_to_terms') is-danger @enderror"/>
                By creating an account you agree to our 
                <a href="https://lana-del-rey-village.myshopify.com/pages/privacy-and-terms-of-use" target="_blank">Privacy Policy &amp; Terms Of Use</a>
                <span class="checkbox-control"></span>
            </label>                        
            @error('agree_to_terms')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
    
    <div class="field">
        <button type="submit" class="button is-primary-dark">Create Your Account</button>
    </div>

    <p>
        Already have an account? <a href="{{ route('login') }}" class="has-text-primary-dark">Login</a>
    </p>
</form>