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
    <div class="card">
        <div class="card-body">
            <page-list
                :initial-pages="{{ json_encode($pages) }}"
                :total-pages="{{ json_encode(count($pages)) }}"
                :store-id="{{ json_encode($store->id) }}">
            </page-list>
        </div>
    </div>
</div>

@endsection
