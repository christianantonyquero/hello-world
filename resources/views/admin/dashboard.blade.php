@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Supmoni Pop-Up Shop Admin</h4>
                    <h5>Stores</h5>
                    <ul>
                    @forelse($stores as $store)
                        <li>{{$store->name}}</li>
                    @empty
                        <li>Please Create your first store.</li>
                    @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
