@extends('layouts.admin')

@section('title', 'Stores')

@section('header-buttons')
    <a href="#" class="btn btn-new" data-toggle="modal" data-target="#addStoreModal">New Store</a>
@endsection

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped store-table">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" style="width:30%">Store Name</th>
                        <th scope="col" style="width:5%">ID</th>
                        <th scope="col" style="width:15%">Slug</th>
                        <th scope="col" style="width:5%">Test Mode?</th>
                       {{-- <th scope="col">Open Times</th> --}}
                        <th scope="col" style="width:25%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(auth()->user()->storeList() as $store)
                        <tr>   
                            <td>{{$store->name}}</td>
                            <td>{{$store->id}}</td>
                            <td>{{$store->slug}}</td>
                            <td>{{$store->is_test_mode ? 'Yes' : 'No' }}</td>
                            {{--
                            <td>
                                <p class="small mb-1">Open At: {{$store->open_at ? $store->open_at->format('m-d-Y H:i') : 'NULL' }}</p>
                                <p class="small">Close At: {{$store->close_at ? $store->close_at->format('m-d-Y H:i') : 'NULL' }}</p>
                            </td>
                            --}}
                            <td class="text-right button-cell">
                                <a href="#" dusk="store-{{ $store->id }}-edit" class="btn btn-sm btn-primary"
                                    data-toggle="modal" data-target="#editStoreModal"
                                    onclick="app.$refs.editStore.loadStore({{ $store->id }})">
                                    Edit
                                </a>
                                <form class="inline-form" action="{{ route('admin.stores.delete', ['store' => $store]) }}" method="POST">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" dusk="store-{{ $store->id }}-delete"
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
</div>

@endsection