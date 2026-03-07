@extends('layouts.admin')

@section('title', 'Products')

@section('header-buttons')
    <a class="btn btn-new" 
       href="{{ route('admin.products.create', ['store' => $store])}}">Add Product</a>
@endsection

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            {{-- products is paginated, so pass in the images for the current page --}}
            <product-list
                :initial-products="{{ json_encode($products->items()) }}"
                :total-products="{{ json_encode($products->total()) }}"
                :store-id={{ json_encode($store->id) }}>
            </product-list>
            {{ $products->links() }} 
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        var store_id = {{ $store->id }};
    </script>
@endpush