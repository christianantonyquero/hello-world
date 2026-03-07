<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StoreLineService;

class LineController extends Controller
{
    public function index(Request $request)
    {
        $total = StoreLineService::totalUsers($request->store);
        return view('admin.store.line.index', ['store' => $request->store, 'total' => $total]);
    }
}
