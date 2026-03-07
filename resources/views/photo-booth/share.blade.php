@extends('layouts.app')
@prepend('body_class')photo-booth-share-page @endprepend


@section('title', 'Photo Moment')
@section('content')
    <div class="pb-share-background-wrapper">
        <section class="pb-content-wrapper">
            <div class="columns">
                <div class="column is-half copy-column">
                    <div class="content">
                        <h1 class="is-size-5 mb-2">{{ config('app.name') }}</h1>
                        <p class="mb-2">
                            Explore the world of Lana Del Rey and get access to exclusive merch!
                        </p>
                        <a href="{{ route('register')}}" class="button">Sign Up Today</a>
                    </div>
                </div>
                <div class="column is-half photo-column">
                    <div class="photo-wrapper">
                        <img class="photo" src="{{$photo->image_url}}" alt=""/>
                    </div>
                    <div class="sharing-icons" id="social-share-app">
                        <social-share-buttons 
                            :networks="social_networks" 
                            :share-url="photo.sharing_url" 
                            :share-media="photo.image._url"
                            :share-title="share_title" 
                            :share-description="share_description" />
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection



@section('sharing_meta')
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Check out my Lana Del Rey Photo Moment" />
    <meta property="og:description" content="See my photo and explore the world of Lana Del Rey" />
    <meta property="og:image" content="{{$photo->image_url}}" />
@endsection

@push('extra_js')
    <script>
        window.__INIT_STATE__ = {
            // load initial state form blade to JS here
            photo: {!! json_encode($photo) !!},
        };
    </script>   
    <script src="{{ mix('js/frontend/social-share-app.js') }}" defer></script>
@endpush