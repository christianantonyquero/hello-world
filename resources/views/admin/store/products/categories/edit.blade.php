
@extends('layouts.admin')


@section('title')Edit Category - {{$category->name}} @endsection

@section('content')

<div class="container-fluid category-edit-page">
    <div class="card">
        <div class="card-body">

        @if ($errors->any())
            <div class="row">
                <div class="col-6">
                    <div class="alert alert-danger mt-2">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="post" action="{{ route('admin.products.categories.update', [$store, $category]) }}" 
              enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="category_name">Category Name</label>
                        <input type="text" id="category_name" name="category_name"
                            class="form-control" 
                            value="{{ old('category_name', $category->name) }}" />
                    </div>

                    <div class="form-group">
                        <label for="store_page">Store Page</label>
                        <select id="store_page" name="store_page" class="form-control">
                            <option value="" selected>-- Select a Page --</option>
                            @foreach($pages as $p)
                                <option {{ old('store_page', $category->store_page_id) == $p->id ? 'selected' : ''}} value="{{$p->id}}">{{$p->name}}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Assigns the Category to a Store Page. This controls
                            which categories are visiable in Page's category drop down.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" min="1" id="display_order"
                            name="display_order"
                            class="form-control"
                            value="{{ old('display_order', $category->display_order) }}" />
                        <small class="form-text text-muted">
                            Controls the order this product category is displayed in the menu
                        </small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 pl-md-4">
                    <div class="form-group border bg-light p-3">
                        @if( $category->background_image )
                            <div class="mb-4 pb-4 border-bottom">
                                <div class="thumb-wrapper mb-2">
                                    <label>Current Background Image</label>
                                    <img src="{{$category->background_image_url}}" class="thumb" style="display: block; max-width: 100px;" />
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="delete_background_image" id="delete_background_image" />
                                    <label  class="form-check-label text-danger" for="delete_background_image">Delete Current Background Image</label>
                                </div>
                            </div>
                         @elseif($category->store_page && $category->store_page->background_image)
                            <div class="store-page-bg-img">
                                <img src="{{ $category->store_page->background_image_url }}"  class="thumb"/>
                                <span class="badge badge-info">Store Page</span>
                            </div>
                        @endif

                        <label for="background_image">New Background Image</label>
                        <input type="file" class="form-control-file" 
                            accept="image/png, image/jpeg" name="background_image"
                            id="background_image">
                        <small class="form-text text-muted mt-4">
                            <p>Optional Background Image used for the Category's page in the store.</p>
                            <p>If no background image is provided the parent Store Page's background image will be used.</p>
                        </small>
                    </div>
                </div>

                <div class="col-md-4 pl-md-4">
                    <div class="form-group border bg-light p-3">
                        @if( $category->thumbnail )
                            <div class="mb-4 pb-4 border-bottom">
                                <div class="thumb-wrapper mb-2">
                                    <label>Current Thumbnail Image</label>
                                    <img src="{{$category->thumbnail_url}}" class="thumb" style="display: block; max-width: 100px;" />
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="delete_thumbnail" id="delete_thumbnail" />
                                    <label  class="form-check-label text-danger" for="delete_thumbnail">Delete Current Thumbnail Image</label>
                                </div>
                            </div>
                        @endif

                        <label for="thumbnail">New Thumbnail Image</label>
                        <input type="file" class="form-control-file"
                            accept="image/png, image/jpeg" name="thumbnail"
                            id="thumbnail">
                        <small class="form-text text-muted mt-4">
                            <p>Optional Thumbnail Image used for the Category's navigation.</p>
                        </small>
                    </div>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-body">
                    <h4 class="card-title">Products</h4>
        
                    <div class="row">
                        @foreach($category->products as $product)
                            <div class="col-6 col-md-4 col-lg-2">
                                <a href="{{ route('admin.products.edit', ['product' => $product, 'store' => $product->store])}}" title="Edit Product">
                                    [{{$product->id}}] {{ $product->name }}
                                </a>
                                <div class="form-group">
                                    <input type="number" min="1" id="product_display_order"
                                        name="product_display_order[{{$product->id}}]"
                                        class="form-control"
                                        value="{{ old('product_display_order', $product->pivot->display_order) }}" />

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="btn-toolbar mt-2 pt-2">
                <div class="btn-group mr-4">
                    <button type="submit" class="btn btn-primary" id="create-category">Save</button>
                </div>
                <div class="btn-group mr-auto">
                    <a href="{{ route('admin.products.categories.index', [$store]) }}" class="btn btn-secondary mr-4">Cancel</a>
                </div>
            </div>
        </form>
    </div></div>
</div>

@endsection
