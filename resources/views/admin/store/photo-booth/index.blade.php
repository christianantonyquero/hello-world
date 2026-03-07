@extends('layouts.admin')

@section('title')Manage Photo Booth - @endsection

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <manage-photo-booth-backdrops 
                :store_id="{{ json_encode($store->id) }}"
                load-backdrop-url="{{ route('store.photo-booth.get-backdrops', [$store]) }}"
                backdrop-create-url="{{ route('admin.photo-booth.backdrop.create', [$store]) }}"
                photo-booth-admin-url="{{ route('admin.photo-booth.index', [$store]) }}">
            </manage-photo-booth-backdrops>

            <div class="card">
                <div class="card-body">
                    <p class="text-info mb-0">You can change the photo booth background image from the store edit screen which is located under the <em>Global Admin -> Store</em> menu.</p>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card">
                <header class="pb-dashboard-user-uploads-header mb-3">
                    <h4 class="section-title">User Uploads</h4>
                    <a href="{{ route('admin.photo-booth.user_upload.index', ['store' => $store]) }}">Manage Uploads</a>
                
                </header>

                <div class="stat">
                    <strong>{{$userImageCount}}</strong>
                    <span class="label">User Uploads</span>
                </div>
            
                <div class="photo-booth-recent-uploads">
                    <h5 class="list-title">Most Recent Uploads</h5>
                    @foreach( $mostRecentImages as $img)
                        <div class="image-info">
                            <div class="img-col">
                                <img src="{{$img->image_url}}"/>
                            </div>
                            <div class="content-col">
                                @if($img->user)
                                    <p class="user"><span>User:</span> {{$img->user->email}}</p>
                                @endif
                                <p class="timestamp"><span>Created At:</span> {{$img->created_at->format('m-d-Y h:i:s')}}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
    
@endsection