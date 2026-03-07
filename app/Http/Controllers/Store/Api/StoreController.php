<?php

namespace App\Http\Controllers\Store\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Store;
use App\StorePage;
use App\ProductCategory;

class StoreController extends Controller
{
    public function getPageMenu(Request $request, Store $store)
    {
        // Get page menu for the store
        return StorePage::with(['hotspots' => function($query ){
                $query->orderBy('display_order', 'asc'); 
            }])->where('store_id', $store->id)->where('hide_from_menu', false)->orderBy('menu_display_order')->get();
    }

    public function getMobileMenu(Request $request, Store $store)
    {
        // Get page menu for the store
        return ProductCategory::where('store_id', $store->id)->where('store_page_id', null)->orderBy('display_order')->get();
    }
}
