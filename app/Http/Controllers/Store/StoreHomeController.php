<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Store;
use Log;

class StoreHomeController extends StoreController
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    
    // Show the 'Default' Store. The First store in the DB
    public function default_store(Request $request) 
    {
        // the default store is the "first store"
        Log::debug('An informational message.');
        try {
            Log::debug('try An informational message.');
            $default_store = cache()->remember('default-store', 60*10, function() {
                $store = Store::default_store();
                return $this->storeEagerLoad($store);
                // return Store::firstOrFail()->load(['pages', 'productCategories', 'products']);
            });
            return $this->storeDisplay($request, $default_store);
        } catch(\Exception $exp) {
            Log::debug('catch An informational message.');
            Log::error($exp);
            $store = Store::default_store();
            $default_store = $this->storeEagerLoad($store);
            return $this->storeDisplay($request, $default_store);
        }
    }

    // Show a Specific Store
    public function show(Request $request, Store $store)
    {
        $store = $this->storeEagerLoad($store);
        // $store->load('pages', 'productCategories', 'products');
        return $this->storeDisplay($request, $store);

    }

    // Store eager loading
    protected function storeEagerLoad(Store $store)
    {
        // Do prefetch unpublished products
        return $store->load(['pages',
            'productCategories',
            'products' => function($query) {
                $query->published();
            }
        ]);
    }

    // Common display logic for store
    protected function storeDisplay(Request $request, Store $store) {
        $user = $request->user();
        // if( Gate::denies('view', $store) ) {
        //     // user is not logged in force them to login
        //     if(!$user) {
        //         // Save url so we are redirected back
        //         if(!session()->has('url.intended')) {
        //             session()->put('url.intended', url()->current());
        //         }
        //         return redirect()->route('login');
        //     } else {
        //         // Access denied
        //         abort(403);
        //     }
        // }

        // Pass data to SPA App
        return view('store.store-spa', ['store' => $store, 'user' => $user]);
    }

}
