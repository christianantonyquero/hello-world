@extends('layouts.admin')

@section('title')
    <small class="text-muted">{{$store->name}} </small><br>
    Songs 
@endsection

@section('header-buttons')
    <button type="button" class="btn small outlined green add-user" 
            data-toggle="modal" data-target="#addStoreSongModal">
        <span class="add-icon"><img src="{{ asset('img/admin/icons/add-circle.svg') }}"></span> New Song
    </button>
@endsection

@section('content')

<div class="container-fluid">
    @empty($songs->items())
        <div class="row">
            <div class="col-sm emptyStateWrapper">
                <h3 class="mb lighter">No songs yet.</h3>
            </div>
        </div>
    @else
    <div class="card">
        <div class="card-body">
            <table class="table admin-list-table page-list-table">
                <thead>
                    <tr>
                        <th scope="col" style="width: 1%;">Artwork</th>
                        <th>Track Name</th>
                        <th>Artist Name</th>
                        <th>Album Name</th>
                        <th>Play Order</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($songs as $song)
                        <tr>
                            <td>
                                @if($song->track_artwork)
                                    <img src="{{ $song->track_artwork_url }}" style="max-width:60px;">
                                @endif
                            </td>
                            <td>
                                {{ $song->track_name }}
                            </td>
                            <td>
                                {{ $song->artist_name }}
                            </td>
                            <td>
                                {{ $song->album_name }}
                            </td>
                            <td>
                                {{ $song->play_order }}
                            </td>
                            <td class="text-right button-cell">
                                <a class="btn btn-primary small" href="{{ route('admin.store.songs.edit', [$store, $song->id]) }}">
                                    Edit
                                </a>
                                
                                <form action="{{ route('admin.store.songs.delete', [$store, $song->id]) }}" method="POST">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn-danger small" dusk="page-{{ $song->id }}-delete"
                                        onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $songs->links() }}
        </div>
    </div>
    @endif
</div>
@include('admin.partials.add_song-modal')
@endsection
