@extends('layouts.admin')


@section('title')Edit Page - {{$page->name}} @endsection

@section('header-buttons')
     <a class="btn btn-new"
        href="{{ route('admin.store.pages.hotspots', [$page->store, $page->id]) }}" title="Edit Hotspots">
        Edit Hotspots
    </a>
@endsection

@section('content')

<div class="container-fluid">
    <edit-store-page :page="{{ json_encode($page) }}"
                     :other-pages="{{ json_encode($otherPages) }}"
                     save-url="{{ route('admin.store.pages.update', [$page->store, $page->id]) }}"
                     success-url="{{ route('admin.store.pages', [$page->store]) }}">
        <template v-slot:cancel-link>
            <a href="{{ route('admin.store.pages', [$page->store]) }}" class="btn btn-secondary mr-4">Cancel</a>
        </template>
    </edit-store-page>
</div>

@endsection
