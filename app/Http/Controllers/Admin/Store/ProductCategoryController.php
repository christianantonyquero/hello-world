<?php

namespace App\Http\Controllers\Admin\Store;

use App\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Store;
use App\StorePages;

use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    public function index(Request $request, Store $store)
    {
        $categories = $store->productCategories()->orderBy('name')->get();
        return view('admin.store.products.categories.index', [
            'categories' => $categories, 
            'store' => $store]);
    }

    public function edit(Request $request, Store $store)
    {
        $pages = $store->pages()->orderBy('name')->get();
        return view('admin.store.products.categories.edit', [
            'category' => $request->productCategory, 'store' => $store,
            'pages' => $pages]);
    }

    public function create(Request $request, Store $store)
    {
        $pages = $store->pages()->orderBy('name')->get();
        return view('admin.store.products.categories.create', [
            'store' => $store, 'pages' => $pages
        ]);
    }

    public function store(Request $request, Store $store)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'display_order' => 'required|integer|gte:1',
            'background_image' => 'nullable|image|max:20000|dimensions:min_width:1400,min_height:800',
            'thumbnail' => 'nullable|image|max:20000|dimensions:min_width:1400,min_height:800',
            'store_page' => ['nullable', 
                Rule::exists('store_pages', 'id')->where(function($query) use($store) {
                    $query->where('store_id', $store->id);
                })],
        ]);

        $category = ProductCategory::create([
            'name' => $validated['category_name'],
            'store_id' => $store->id,
            'store_page_id' => $validated['store_page'] ?? null,
            'display_order' => $validated['display_order'] ?? 1,
        ]);

        if($validated['background_image'] ?? false) {
            $category->background_image = $request->file('background_image')->store('product_categories');
            $category->save();
        }

        if($validated['thumbnail'] ?? false) {
            $category->thumbnail = $request->file('thumbnail')->store('product_categories');
            $category->save();
        }
        return redirect(route('admin.products.categories.index', $request->store));
    }

    public function update(Request $request, Store $store, ProductCategory $category)
    {
        // dd($request->product_display_order);
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'display_order' => 'required|integer|gte:1',
            'background_image' => 'nullable|image|max:20000|dimensions:min_width:1400,min_height:800',
            'product_display_order' => 'required|array',
            'thumbnail' => 'nullable|image|max:20000|dimensions:min_width:1400,min_height:800',
            'store_page' => ['nullable', 
                Rule::exists('store_pages', 'id')->where(function($query) use($store) {
                    $query->where('store_id', $store->id);
                })],
            'delete_background_image' => 'nullable',
            'delete_thumbnail' => 'nullable'
        ]);

        $category->update([
            'name' => $validated['category_name'],
            'store_id' => $store->id,
            'store_page_id' => $validated['store_page'] ?? null,
            'display_order' => $validated['display_order'] ?? 1,
        ]);

        if($validated['background_image'] ?? false) {
            $category->background_image = $request->file('background_image')->store('product_categories');
            $category->save();
        }

        if($validated['delete_background_image'] ?? false) {
            $category->background_image = null;
            $category->save();
        }

        if($validated['thumbnail'] ?? false) {
            $category->thumbnail = $request->file('thumbnail')->store('product_categories');
            $category->save();
        }

        if($validated['delete_thumbnail'] ?? false) {
            $category->thumbnail = null;
            $category->save();
        }

        $product_display_order_list = [];
        foreach($request->product_display_order as $key=>$value) {
            $product_display_order_list[$key] = ['display_order' => $value ?? 10];
        }

        $category->products()->sync($product_display_order_list);

        return redirect(route('admin.products.categories.index', $request->store));

    }

    public function destroy(Request $request)
    {
        $request->productCategory->delete();
        return redirect(route('admin.products.categories.index', $request->store));
    }
}
