<?php

namespace App\Http\Controllers\Admin\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        auth()->user()->switchStoreContext($request->store);

        return view('admin.store.dashboard', ['store' => $request->store]);
    }
}
