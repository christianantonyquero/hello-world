<?php

namespace App\Http\Controllers\Store\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Store;
use App\Product;

class ProductController extends Controller
{
    // All Products
    public function index(Request $request, Store $store)
    {
        /***  COMMENT OUT Age Gate Check
              $store->products()->ageGate()->get()->load('sizes', 'images');
        */
        return $store->products()->published()->get()->load('sizes', 'images');
    }

    // Show Product
    public function show(Request $request, Store $store, Product $product)
    {
        abort_unless($product->store->is($store), 404, 'Invalid Product For Store');
        /***  COMMENT OUT Age Gate Check
           abort_unless($product->ageGateAccess(), 404);
        */

        // Do not return unpublished products
        abort_unless($product->is_published, 404, 'Product Not Found');

        // Previous code when there was no is_available
        // $product =  $product->load('sizes', 'images', 'categories');
        
        $product =  $product->load(['sizes' => function ($query) {
            $query->where('is_available', true);
        }, 'images', 'categories']);

        return $product;
    }

    // Suggested Products
    public function suggested(Store $store)
    {
        $products = $store->products()->suggested()->get()->load('sizes', 'images');
        return $products;
    }
}
