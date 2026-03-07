@extends('layouts.admin')

@section('title', 'Orders')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <strong>Order # {{ $order->id }}</strong> <br>
                </div>
                <div class="col-6">
                    <strong>Date:</strong> {{ $order->created_at->format('F j, Y - g:ia') }} <br>
                    <strong>Store:</strong> <a href="{{ route('admin.store.dashboard', $order->store) }}">{{ $order->store->name }}</a> <br>
                </div>
            </div>
            <div class="row mt-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Product</th>
                            <th scope="col">Size</th>
                            <th scope="col">Quantity</th>
                            <th scope="col" class="text-right">Cost</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td><img src="{{ $item->product->thumbnail_url }}" style="max-width: 80px;"></td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->size ? $item->size->size : '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="text-right">$ {{ $item->subtotal }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4"></td>
                            <td>Subtotal</td>
                            <td class="text-right">$ {{ $order->subtotal }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td>Taxes</td>
                            <td class="text-right">$ {{ $order->taxes }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td>Total</td>
                            <td class="text-right">$ {{ $order->total }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
