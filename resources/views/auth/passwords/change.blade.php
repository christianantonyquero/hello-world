@extends('layouts.user-profile')

@section('title', 'Change Password ')

@push('body_class', 'change-password-page ')

@section('content_user_profile')
<div class="columns">
    <div class="column">
        <div class="box">
            <h3 class="is-size-3 mb-5 has-text-primary">Change Password</h3>
        
            <form method="POST" action="{{ route('user-profile.password.update') }}" 
                class="auth-form password-change-form">
                @csrf
                @method('PATCH')

                <div class="field">
                    <label class="label" for="current_password">Current Password</label>
                    <input type="password" class="input @error('current_password') is-danger @enderror is-medium" 
                        id="current_password" name="current_password" required />
                    
                    @error('current_password')
                        <p class="help is-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label class="label" class="label" for="password">New Password</label>
                    <input type="password" class="input @error('password') is-danger @enderror is-medium" 
                        id="password" name="password"  required/>
                    
                    @include('partials._password-rules')
                    @error('password')
                        <p class="help is-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label class="label" for="password_confirmation">Confirm New Password</label>
                    <input type="password" class="input @error('password_confirmation') is-danger @enderror is-medium" 
                        id="password_confirmation" name="password_confirmation" required />
                    
                    @error('password_confirmation')
                        <p class="help is-danger">{{ $message }}</p>
                    @enderror
                </div>

                <hr/>

                <div class="field is-grouped">
                    <div class="control">
                        <button type="submit" class="button is-primary">Change Password</button>  
                    </div>
                    <div class="control">
                        <a href="{{ route('user-profile.index')}}" class="button is-primary is-outlined">Back</a>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection