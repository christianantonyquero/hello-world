@extends('layouts.admin')
@section('title', 'Photo Booth Uploads')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped pb-uploads-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>User</th>
                            <th>Created At</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($uploads as $img)
                        <tr>
                            <td>
                                <a href="{{$img->image_url}}" target="_blank">
                                    <img class="user-image" src="{{$img->image_url}}" alt=""/>
                                </a>
                            </td>
                            <td>
                                @if($img->user)
                                    {{$img->user->email}}
                                @endif
                            </td>
                            <td>
                                {{$img->created_at->format('m-d-Y h:i:s')}}
                            </td>
                            <td>
                                <a class="share-link" href="{{ $img->sharing_url }}" target="_blank">Sharing Url</a>
                            </td>
                            <td>
                                <form action="{{ route('admin.photo-booth.user_upload.delete', [$store, $img->id]) }}" method="POST">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" 
                                        class="btn btn-danger small"
                                        onclick="return confirm('Are you sure you want to delete this photo?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$uploads->links()}}
            </div>
        </div>
    </div>
@endsection