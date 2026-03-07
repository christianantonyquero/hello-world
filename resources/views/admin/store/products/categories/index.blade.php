@extends('layouts.admin')

@section('title', 'Product Categories')

@section('header-buttons')
    <a class="btn btn-new" href="{{ route('admin.products.categories.create', [$store]) }}">
        Add Category
    </a>
@endsection

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped category-table">
                <thead class="thead-light">
                    <tr>
                        <th class="cell-name" scope="col">Name</th>
                        <th scope="col">ID</th>
                        <th scope="col">Store Page</th>
                        <th class="cell-thumb" scope="col">Background Image</th>
                        <th class="cell-thumb" scope="col">Thumbnail</th>
                        <th scope="col" class="text-center col-narrow">Number of Products</th>
                        <th scope="col" class="text-center col-narrow">Display Order</th>


                        <th scope="col" class="cell-buttons"></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td class="cell-name">
                                <a href="{{ route('admin.products.categories.edit', ['productCategory' => $category, 'store' => $store]) }}" title="Edit">{{$category->name}}</a>
                            </td>
                            <td>
                                {{$category->id ?? ''}}
                            </td>
                            <td>
                                {{$category->store_page->name ?? ''}}
                            </td>
                            <td class="cell-thumb">
                                @if($category->background_image)
                                    <img src="{{ $category->background_image_url }}" class="thumb" />
                                @elseif($category->store_page && $category->store_page->background_image)

                                    <div class="store-page-bg-img">
                                        <img src="{{ $category->store_page->background_image_url }}"  class="thumb"/>
                                        <span class="badge badge-info">Store Page</span>
                                    </div>
                                @endif
                            </td>
                            <td class="cell-thumb">
                                @if($category->thumbnail)
                                    <img src="{{ $category->thumbnail_url }}" class="thumb" />
                                @endif
                            </td>
                            <td class="text-center col-narrow">
                                {{$category->products()->count()}}
                            </td>
                            <td class="text-center col-narrow">
                                {{$category->display_order}}
                            </td>
                            <td class="text-right button-cell">
                                <a class="btn btn-primary btn-sm" href="{{ route('admin.products.categories.edit', ['productCategory' => $category, 'store' => $store]) }}">Edit</a>
                                
                                <form class="inline-form" action="{{ route('admin.products.categories.delete', ['productCategory' => $category, 'store' => $store]) }}" 
                                        method="POST">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        var store_id = {{ $store->id }};
    </script>
@endpush
