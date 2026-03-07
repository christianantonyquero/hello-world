<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;

class ProductController extends StoreController
{
    public function show(Request $request)
    {
        $this->checkCartStore();
        return view('store.products.show', ['product' => $request->product]);
    }
}
