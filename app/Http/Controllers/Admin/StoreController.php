<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Store;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Validation\Rule;

class StoreController extends Controller
{
    public function index()
    {
        return view('admin.store.index');
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:250'],
            'slug' => [
                'required', 
                'max:250',
                'alpha_dash',
                'unique:stores',
            ],
            'intro_video' => 'nullable|url',
            'cart_bg_image' => 'nullable|image|max:20000|dimensions:min_width:1400,min_height:800',
            'photo_booth_bg_image' => 'nullable|image|max:20000|dimensions:min_width:1400,min_height:800'
        ]);

        $store = new Store([
            'name' => $validated['name'], 
            'slug' => $validated['slug'],  
            'intro_video' => $validated['intro_video'] ?? null,
        ]);
        $store->is_test_mode = $request->is_test_mode ? true : false;

        if ($request->hasFile('logo')) {
            $store->logo = $request->file('logo')->store('logos', 'public');
        }

        if( $validated['cart_bg_image'] ?? false ) {
            $store->cart_bg_image = $request->file('cart_bg_image')->store('store/background_images');
        }

        if( $validated['photo_booth_bg_image'] ?? false ) {
            $store->photo_booth_bg_image = $request->file('photo_booth_bg_image')->store('store/background_images');
        }

        $store->save();

        return $store;
    }

    public function show(Request $request)
    {
        return $request->store;
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:250'],
            'slug' => [
                'required', 
                'max:250',
                'alpha_dash', 
                Rule::unique('stores')->ignore($store->id)
            ],
            'intro_video' => 'nullable|url',
            'cart_bg_image' => 'nullable|image|max:20000|dimensions:min_width:1400,min_height:800',
            'photo_booth_bg_image' => 'nullable|image|max:20000|dimensions:min_width:1400,min_height:800'
        ]);

        $store->update([
            'name' => $validated['name'], 
            'slug' => $validated['slug'],  
            'intro_video' => $validated['intro_video'] ?? null,
        ]);

        $store->is_test_mode = $request->is_test_mode ? true : false;

        if ($request->hasFile('logo')) {
            $store->logo = $request->file('logo')->store('logos', 'public');
        }

        if( $validated['cart_bg_image'] ?? false ) {
            $store->cart_bg_image = $request->file('cart_bg_image')->store('store/background_images');
        }

        if( $validated['photo_booth_bg_image'] ?? false ) {
            $store->photo_booth_bg_image = $request->file('photo_booth_bg_image')->store('store/background_images');
        }

        $store->save();

        return $store;
    }

    public function destroy(Request $request)
    {
        $request->store->delete();
        return redirect(route('admin'));
    }
}
