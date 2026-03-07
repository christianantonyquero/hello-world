<?php

namespace App\Http\Controllers\Store\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\StoreSong;
use App\Store;

class StoreSongController extends Controller
{
    public function get_store_playlist(Request $request, Store $store)
    {
        // Return JSON list of a Stores Songs
        return $store->songs()->orderBy('play_order')->get();
    }
}
