@extends('layouts.admin')

@section('title')Edit Hotspots for Page - {{$page->name}} @endsection

@section('content')

<div class="container-fluid">
    <edit-store-page-hotspots 
        :page="{{ json_encode($page) }}"
        :hotspot-types="{{ json_encode($hotspot_types) }}"
        :all-pages = "{{ json_encode($allStorePages) }}"
        :products = "{{ json_encode($products) }}"
        :categories = "{{ json_encode($categories) }}"
        create-url="{{ route('admin.store.pages.hotspots.create', [$page->store, $page->id]) }}"
        hotspot-url-base="{{ route('admin.store.pages.hotspots.create', [$page->store, $page->id]) }}">
</div>

@endsection
