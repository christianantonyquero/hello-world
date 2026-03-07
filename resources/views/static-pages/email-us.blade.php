@extends('layouts.user-profile')

@section('title', 'Email Us')

@section('content_user_profile')
<div class="columns is-multiline">
  <div class="column is-full ">
      <div class="profile-intro box">
          <div class="columns">
              <div class="column name-col">
                  <h5>Give us Feedback</h5>
              </div>
          </div>
      </div>
  </div>
  <div class="column is-full edit-profile-col">
    <div class="box">
        <form method="POST" class="auth-form" action="{{ route('email-us') }}">
            @csrf
            @honeypot
            {{-- <input type="hidden" class="hidden" name="email" value=""/> --}}

            <div class="field">
              <label for="email" class="label">E-Mail address</label>
              <div class="control">
              <input id="email" name="email" type="email" required autofocus
                class="input @error('email') is-danger @enderror is-medium">
                  
                @error('name')
                  <p class="help is-danger">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="field">
                <label class="label" for="message">Message</label>
                <div class="control">
                <input id="message" name="message" required
                    placeholder="Have some suggestion or feedback?"/>
    
                    @error('message')
                        <p class="help is-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <button type="submit" class="button is-primary mt-2">SEND</button>
                </div>
            </div>
        </form>
    </div>
  </div>
</div>
@endsection