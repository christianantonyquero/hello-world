@extends('layouts.user-profile')

@section('title', 'Thank you for the feedback')

@section('content_user_profile')
<div class="columns is-multiline">
  <div class="column is-full ">
      <div class="profile-intro box">
          <div class="columns">
              <div class="column name-col">
                  <h5>Thank you for giving us feedback</h5>
              </div>
          </div>
      </div>
  </div>
  <div class="column is-full edit-profile-col">
    <div class="box">
        <a href="{{route('home')}}" target="_self">Continue shopping</a>
    </div>
  </div>
</div>
@endsection