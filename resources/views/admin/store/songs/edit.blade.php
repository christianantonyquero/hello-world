@extends('layouts.admin')


@section('title')Edit Song @endsection

@section('content')

<div class="container-fluid">
    <edit-store-song 
        :song="{{ json_encode($song) }}"
        save-url="{{ route('admin.store.songs.update', [$store, $song->id]) }}"
        success-url="{{ route('admin.store.songs', [$store]) }}">
        <template v-slot:cancel-link>
            <a href="{{ route('admin.store.songs', [$store]) }}" class="btn btn-secondary mr-4">Cancel</a>
        </template>
    </edit-store-song>
</div>

@endsection