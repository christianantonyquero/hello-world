<?php

namespace App\Http\Controllers\Store;

use Cart;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    public function checkCartStore()
    {
        // check if cart context has switched, clear cart if so
        if(Cart::getTotalQuantity() > 0){
            $storeId = Cart::getContent()->first()->associatedModel->store_id;
            if($storeId != app()->request->store->id){
                Cart::clear();
            }
        }
    }
}
