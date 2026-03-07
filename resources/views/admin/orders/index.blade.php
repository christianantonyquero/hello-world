@extends('layouts.admin')

@section('title', 'Orders')

@section('content')

<div class="container-fluid">
    @empty($orders->items())
        <div class="row">
            <div class="col-sm emptyStateWrapper">
                <h3 class="mb lighter">No orders yet.</h3>
            </div>
        </div>
    @else
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Order #</th>
                        <th scope="col">Order Date</th>
                        <th scope="col">Total</th>
                        <th scope="col">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                @if($context == 'store')
                                    <a href="{{ route('admin.store.orders.show', [$store, $order]) }}">{{ $order->id }}</a>
                                @else
                                    <a href="{{ route('admin.orders.show', $order) }}">{{ $order->id }}</a>
                                @endif
                            </td>
                            <td>{{ $order->dateShort() }}</td>
                            <td>${{ number_format($order->total, 2) }}</td>
                            <td>{{ $order->statusString() }}</td>
                            <td>
                                <!-- Actions -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $orders->links() }}
        </div>
    </div>
    @endif
</div>

@endsection
