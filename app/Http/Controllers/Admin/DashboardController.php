<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Store;

class DashboardController extends Controller
{
    public function index()
    {
        $stores = Store::get();
        return view('admin.dashboard', ['stores' => $stores]);
    }
}
