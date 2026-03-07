<?php

namespace App\Http\Controllers\Store\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Store;
use App\ProductCategory;
use App\Song;
use Log;

class CategoryController extends Controller
{
    // Categories for a store
    public function index(Request $request, Store $store)
    {
        return $store->productCategories;
    }
    public function products(Request $request, Store $store, ProductCategory $category)
    {
        // Make sure the category belongs to the store
        abort_unless( $category->store->is($store), 404, "Invalid Catgory For Store");

        // It is possible to load products and assign them to a store,
        // but use a category from another store. This filters that out
        $s = $category->load(['products' => function($query) use($store) {
            /***  COMMENT OUT Age Gate Query 
                  $query->where('store_id', $store->id)->ageGate()->with(['sizes', 'images']);
            */
            $query->orderBy('product_product_category.display_order')->where('store_id', $store->id)->published()->with(['sizes', 'images']);
        }, 'store_page', 'store_page.parent' ]);
        // $b = $category->products->display_order;
        // Log::info(["b"=>$b]);
        return $s;
    }
}
