@extends('layouts.admin')

@section('title')
    <small class="text-muted">{{$store->name}} </small><br>
    Store Pages
@endsection

@section('header-buttons')
    <a class="btn btn-new" href="{{ route('admin.store.pages.new', [$store]) }}">Add Page</a>
@endsection

@section('content')

<div class="container-fluid">
    <p>Total pages: {{ $pages->count() }}</p>
    @foreach($pages as $page)
        <div>
            <a href="{{ route('admin.store.pages.edit', [$store, $page->id]) }}">{{ $page->name }}</a>
            - {{ $page->slug }}
            [<a href="{{ route('admin.store.pages.hotspots', [$store, $page->id]) }}">{{ $page->hotspots()->count() }} hotspots</a>]
        </div>
    @endforeach
</div>

@endsection
