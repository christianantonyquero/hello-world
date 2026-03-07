<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Store;
use App\StorePage;
use App\StorePageHotspot;

use App\Http\Requests\Admin\UpdateHotspot;

class HotspotController extends Controller
{
    public function index(Request $request)
    {
        $hotspot_types = StorePageHotspot::HOTSPOT_TYPES();
        $page = StorePage::with('hotspots')->findOrFail($request->page_id);
        $store = $page->store;
        $allStorePages = $store->pages;
        $products = $store->products;
        $categories = $store->productCategories;
        
        return view('admin.store.pages.edit_hotspots', [
            'page' => $page,
            'allStorePages' => $allStorePages,
            'products' => $products,
            'categories' => $categories,
            'hotspot_types' => $hotspot_types]);
    }

    public function create(UpdateHotspot $request)
    {
        $page = StorePage::with('hotspots')->findOrFail($request->page_id);
        $validated = $request->validated();
        $hotspot = new StorePageHotspot([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'display_order' => $validated['display_order'],
            'x' => $validated['x'],
            'y' => $validated['y'],
            'video' => $validated['video'] ?? null,
            'store_page_id' => $page->id
        ]);

        $hotspot->save();
        return response()->json(['success' => true, 'hotspot' => $hotspot]);
    }

    public function update(UpdateHotspot $request)
    {
        $hotspot = StorePageHotspot::findOrFail($request->id);
        $validated = $request->validated();

        $hotspot->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'display_order' => $validated['display_order'],
            'x' => $validated['x'],
            'y' => $validated['y'],
            'video' => $validated['video'] ?? null,
        ]);

        return response()->json(['success' => true, 'hotspot' => $hotspot]);

    }

    public function destroy(Request $request)
    {
        $hotspot = StorePageHotspot::findOrFail($request->id);
        $hotspot->delete();
        return response()->json(['success' => true]);
    }
}
