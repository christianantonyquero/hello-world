@extends('layouts.admin')

@section('title', $store->name)

@section('content')

<div class="container-fluid">
    <div class="row align-items-center">
        <div class="col-12 is-flex-row not-justified page-intro">
            <h2>Store Dashboard</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <p><strong>Store ID:</strong> {{$store->id}}</p>
                    <p><strong>Slug:</strong> {{$store->slug}}</p>
                    @if($store->logo)
                        <p>
                            <strong>Logo:</strong> 
                            <img src="{{ asset($store->logo) }}" alt="" style="max-width:100%;">
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    @if($store->cart_bg_image)
                        <div class="image-thumb-wrapper mb-4">
                            <label>Cart Background Image</label>
                            <img src="{{ $store->cart_bg_image_url }}" class="thumb" />
                        </div>
                    @endif

                    @if($store->photo_booth_bg_image)
                        <div  class="image-thumb-wrapper mb-4">
                            <label>Photo Booth Background Image</label>
                            <img src="{{ $store->photo_booth_bg_image_url }}" class="thumb" />
                        </div>
                    @endif

                    @if($store->intro_video)
                        <a class="btn outlined" href="{{ $store->intro_video }}" target="_blank">Intro Video</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        var store_id = {{ $store->id }};
    </script>
@endpush

@endsection
