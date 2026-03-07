<?php

namespace App\Http\Controllers\Store\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Store;
use App\StorePage;

class PageController extends Controller
{
    public function index(Request $request, Store $store)
    {
        return $store->pages;
    }

    public function show(Request $request, Store $store)
    {
        $pageSlug = $request->slug ?? '/';
        $storePage = $store->pages()->where('slug', $pageSlug)->firstOrFail();

        // Make sure page and store match
        abort_unless($storePage->store->is($store), 404, "Invalid page for store");
        return $storePage->load([
            'hotspots' => function($query) {
                $query->orderBy('display_order', 'ASC');
            }, 
            'categories' => function($query) {
                $query->orderBy('name', 'ASC');
            }, 
            'parent']);
    }
}
