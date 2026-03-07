@extends('layouts.admin')


@section('title')Edit Product - {{$product->name}} @endsection

@section('content')
    <div class="container-fluid">
        <edit-product
            :product-prop="{{ json_encode($product) }}"
            save-url="{{ route('admin.products.update', ['product' => $product, 'store' => $product->store]) }}"
            success-url="{{ route('admin.products.index', [$product->store]) }}"
            delete-url="{{ route('admin.products.delete', ['product' => $product, 'store' => $product->store]) }}">
            <template v-slot:cancel-link>
                <a class="btn btn-secondary" href="{{ route('admin.products.index', ['store' => $product->store]) }}">
                    Cancel
                </a>
            </template>
        </edit-product>
    </div>
@endsection