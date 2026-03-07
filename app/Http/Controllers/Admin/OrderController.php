<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::paid()->orderBy('created_at','desc')->simplePaginate(15);
        return view('admin.orders.index', ['orders' => $orders, 'context' => 'admin']);
    }

    public function show(Request $request)
    {
        if($request->order->status == Order::STATUS_COMPLETE){
            return view('admin.orders.show', ['order' => $request->order]);
        }
        else{
            abort(404);
        }
    }
}
