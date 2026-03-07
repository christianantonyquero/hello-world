<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;

class LineRedirectController extends StoreController
{
    public function index(Request $request)
    {
        // store code in session for later use
        $request->session()->put('store_codes.'.$request->store->id, $request->code);

        // redirect to store (will go to login/signup if needed)
        return redirect(route('store', $request->store));
    }
}
