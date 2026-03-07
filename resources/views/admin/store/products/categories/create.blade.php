@extends('layouts.admin')

@section('title')Create Category @endsection

@section('content')

<div class="row">
    <div class="col-12 card">
        <div class="row">
            <div class="col">
                <h3>Add Category</h3>
            </div>
        </div>
        @if ($errors->any())
            <div class="row"><div class="col-6">
                <div class="alert alert-danger mt-2">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div></div>
        @endif

        <form method="post" action="{{ route('admin.products.categories.store', $store) }}"
              enctype="multipart/form-data">
            @csrf
            <div class="col-6">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input autocomplete="off" type="text" class="form-control" name="category_name" id="name"
                        aria-describedby="name"
                        value="{{ old('category_name') }}">
                </div>

                <div class="form-group">
                    <label for="store_page">Store Page</label>
                    <select id="store_page" name="store_page">
                        <option value="" selected >-- Select a Page --</option>
                        @foreach($pages as $p)
                            <option value="{{$p->id}}">{{$p->name}}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">
                        Assigns the Category to a Store Page. This controls 
                        which categories are visiable in Page's category drop down.
                    </small>
                </div>

                <div class="form-group">
                    <label for="background_image">Background Image</label>
                    <input type="file" class="form-control-file"
                        accept="image/png, image/jpeg"
                        name="background_image" id="background_image">
                    <small class="form-text text-muted">
                        Background Image used for the Category's page in the store
                    </small>
                </div>

                <div class="form-group">
                    <label for="thumbnail">Thumbnail Image</label>
                    <input type="file" class="form-control-file"
                        accept="image/png, image/jpeg"
                        name="thumbnail" id="thumbnail">
                    <small class="form-text text-muted">
                        Thumbnail Image used for the Category's navigation
                    </small>
                </div>

                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" min="1" id="display_order"
                        name="display_order"
                        class="form-control" />
                    <small class="form-text" >
                        Controls the order this product category is displayed in the menu
                    </small>
                </div>
            </div>
            <div class="row mt-2">
                <a href="{{ route('admin.products.categories.index', [$store]) }}" class="btn btn-secondary mr-4">Cancel</a>
                <button type="submit" class="btn btn-primary" id="create-category">Create</button>
            </div>
        </form>
               
    </div>
</div>


@endsection