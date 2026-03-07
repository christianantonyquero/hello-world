@extends('layouts.admin')

@section('title')New Product @endsection

@section('content')
<div class="container-fluid">
    <create-product
        save-url="{{ route('admin.products.store', [$store]) }}"
        success-url="{{ route('admin.products.index', [$store]) }}"
        :store_id={{$store->id}}>
        <template v-slot:cancel-link>
            <a class="btn btn-secondary" href="{{ route('admin.products.index', ['store' => $store]) }}">
                Cancel
            </a>
        </template>
    </create-product>
</div>
@endsection