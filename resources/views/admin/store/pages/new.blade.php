@extends('layouts.admin')


@section('title')New Page @endsection

@section('content')

<div class="container-fluid">
    <create-store-page save-url="{{ route('admin.store.pages.create', [$store]) }}"
        :all-pages="{{ json_encode($allPages) }}"
        success-url="{{ route('admin.store.pages', [$store]) }}">
        <template v-slot:cancel-link>
            <a class="btn btn-secondary mr-4" href="{{ route('admin.store.pages', ['store' => $store]) }}">
                Cancel
            </a>
        </template>
    </create-store-page>
</div>

@endsection