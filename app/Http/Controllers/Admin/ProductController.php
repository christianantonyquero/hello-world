<?php

namespace App\Http\Controllers\Admin;

// use Gate;
use App\Store;
use App\Product;
use App\ProductSize;
use App\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Log;

class ProductController extends Controller
{
    public function index(Request $request, Store $store)
    {
        return view('admin.store.products.index', [
            'products' => $store->products()->with('categories', 'store')->paginate('500'),
            'store' => $store]);
    }

    public function create(Request $request, Store $store)
    {
        return view('admin.store.products.create', ['store' => $store]);
    }

    public function store(Request $request, Store $store)
    {
        if (is_string($request->sizes)) {
            $request->sizes = json_decode($request->sizes, true);
        }
        
        $thumb_validation_rules = [
            'required',
            'image', 
            'max:1000', // Max 1MB
            Rule::dimensions()->maxWidth(350)->maxHeight(350),
        ];
        $thumb_hover_validation_rules = [
            'image', 
            'max:1000', // Max 1MB
            Rule::dimensions()->maxWidth(350)->maxHeight(350),
        ];
        
        $messages = [
            'thumbnail.dimensions' => 'The thumbnail must be less than 350x350',
            'thumbnail_hover.dimensions' => 'The hover thumbnail must be less than 350x350',
            'categories.required' => 'There must be atleast 1 category',
            'categories.regex' => 'There must be atleast 1 category'
        ];

        // Simple but not complete validation
        $validated = $request->validate([
            'name' => 'required|max:200',
            'price' => 'required|numeric',
            'description' => 'required:string',
            'categories' => 'required|regex:/\d/', // Regex means that string should atleast have 1 digit so if there is no number the product gets rejected
            'max_cart_quantity' => 'nullable|integer|gte:1',
            'thumbnail' => $thumb_validation_rules,
            'thumbnail_hover' => $thumb_hover_validation_rules,
        ], $messages);

        // Size Validation
        $this->validateProductSizes($request);

        // Create Product
        $product = new Product($request->only(
            'name', 'description', 'price', 
            'current_inventory', 'minimum_age', 'ext_purchase_url',
            'max_cart_quantity',
            'shopify_variant_id'));
        
            $product->store_id = $store->id;
            
            if($request->has('not_for_sale')) {
                $product->not_for_sale = true;
            } else {
                $product->not_for_sale = false;
            }

            if($request->has('is_suggested')) {
                $product->is_suggested = true;
            } else {
                $product->is_suggested = false;
            }

            if($request->has('is_published')) {
                $product->is_published = true;
            } else {
                $product->is_published = false; // default on this field is true, so make sure we set it to false
            }
            
            // Update Images if needed
            $thumbnail = $request->thumbnail;
            if( $thumbnail ) {
                $product->thumbnail = $thumbnail->store('products/thumbs');
                $product->save();
            }
            
            $thumbnail_hover = $request->thumbnail_hover;
            if( $thumbnail_hover ) {
                $product->thumbnail_hover = $thumbnail_hover->store('product/thumb-hovers');
                $product->save();
            }

            $categories = json_decode($request->categories);
            $product->categories()->sync($categories);

            if (is_string($request->sizes)) {
                $request->sizes = json_decode($request->sizes, true);
            }
            
            $product->addSizes($request->sizes);

            if($request->hasFile('images')) {
                foreach($request->file('images') as $image) {
                    $path = $image->store('product_images');
                    $product->addImage($path);
                }
            }

            // Save Product
            $product->save();
            return response()->json(['success' => true, 'product' => $product], 201);
    }

    public function edit(Request $request)
    {
        $request->product->load('sizes', 'categories', 'images', 'store');
        return view('admin.store.products.edit', ['product' => $request->product]);
    }

    public function update(Request $request, Store $store, Product $product)
    {
        $thumb_validation_rules = [
            'image', 
            'max:1000', // Max 1MB
            Rule::dimensions()->maxWidth(350)->maxHeight(350),
        ];
        $messages = [
            'thumbnail.dimensions' => 'The thumbnail must be less than 350x350',
            'thumbnail_hover.dimensions' => 'The hover thumbnail must be less than 350x350',
            'categories.required' => 'There must be atleast 1 category',
            'categories.regex' => 'There must be atleast 1 category'
        ];

        // Simple but not complete validation
        $validated = $request->validate([
            'thumbnail' => $thumb_validation_rules,
            'thumbnail_hover' => $thumb_validation_rules,
            'categories' => 'required|regex:/\d/', // Regex means atleast 1 digit so if there is no number the product gets rejected
        ], $messages);

        $productData = json_decode($request->product);

        // Product validation, must convert to object first
        $productRules = [
            'name' => 'required|max:200',
            'price' => 'required|numeric',
            'description' => 'required:string',
            'max_cart_quantity' => 'nullable|integer|gte:1',
            'sizes.*.size' => 'required|string|max:200',
            'sizes.*.price' => 'required|numeric',
            'sizes.*.display_order' => 'required|numeric',
        ];

        $productValidator = Validator::make(json_decode($request->product, true), $productRules, [], [
            'sizes.*.size' => 'Product Sizes - Size',
            'sizes.*.price' => 'Product Sizes - Price',
            'sizes.*.display_order' => 'Product Sizes - Display Order',
        ]);

        $productValidator->validate();
        
        $removedImages = json_decode($request->removedImages);

        $product->update([
            'name' => $productData->name,
            'description' => $productData->description,
            'price' => $productData->price,
            'is_published' => $productData->is_published ? true : false,
            'max_cart_quantity' => $productData->max_cart_quantity ? $productData->max_cart_quantity : null,
            'current_inventory' => $productData->current_inventory ? $productData->current_inventory : null,
            'minimum_age' => $productData->minimum_age ? $productData->minimum_age : null,
            'ext_purchase_url' => $productData->ext_purchase_url ? $productData->ext_purchase_url : null,
            'not_for_sale' => $productData->not_for_sale ? true : false,
            'is_suggested' => $productData->is_suggested ? true : false,
            'shopify_variant_id' => $productData->shopify_variant_id ? $productData->shopify_variant_id : null,
        ]);

        // Update Images if needed
        $thumbnail = $request->thumbnail;
        if( $thumbnail ) {
            $product->thumbnail = $thumbnail->store('products/thumbs');
            $product->save();
        }

        $thumbnail_hover = $request->thumbnail_hover;
        if( $thumbnail_hover ) {
            $product->thumbnail_hover = $thumbnail_hover->store('product/thumb-hovers');
            $product->save();
        }

        // update/add sizes
        foreach($productData->sizes as $sizeData)
        {
            if($sizeData->id){
                $size = ProductSize::find($sizeData->id);
            }
            else{
                $size = new ProductSize();
                $size->product_id = $product->id;
            }
            $size->size = $sizeData->size;
            $size->price = $sizeData->price;
            $size->shopify_variant_id = $sizeData->shopify_variant_id ? $sizeData->shopify_variant_id : null;
            $size->display_order = $sizeData->display_order;
            $size->is_available = $sizeData->is_available;
            $size->save();
        }

        // remove some old images images
        $product->removeImages($removedImages);

        // add new images
        if($request->hasFile('images')) {
            foreach($request->file('images') as $image) {
                $path = $image->store('product_images');
                $product->addImage($path);
            }
        }

        // remove any sizes no longer present
        $product->sizes()->whereNotIn('size', collect($productData->sizes)->pluck('size'))->delete();

        $categories = json_decode($request->categories);
        Log::info(['categories'=>$categories]);
        $product->categories()->sync($categories);

        return response()->json(['success' => true, 'product' => $product]);
    }

    public function destroy(Request $request)
    {
        $request->product->delete();
        return response()->json(['success' => true]);
        // return redirect(route('admin.products.index', $request->store));
    }

    public function deleteMany(Request $request, Store $store)
    {
        // Get the product IDs and only delete products for this store.
        // Just in case we support multiple stores with different products in the future
        $productIDs = collect($request->get('productIDs', []));
        if( $productIDs->isNotEmpty()) {
            $store->products()->whereIn('id', $productIDs)->delete();
        }
        return response()->json(['success' => true]);
        // return redirect(route('admin.products.index', $request->store));
    }

    public function updatePublishStatusMany(Request $request, Store $store)
    {
        // Update the publish status on multiple products
        $productIDs = collect($request->get('productIDs', []));
        $is_published = $request->get('is_published', false);
        if( $productIDs->isNotEmpty() ) {
            Product::where('store_id', $store->id)->whereIn('id', $productIDs)->update(['is_published' => $is_published]);
        }
        
        return response()->json(['success' => true]);
    }

    protected function validateProductSizes(Request $request)
    {
        $sizeRules = [
            '*.size' => 'required|string|max:200',
            '*.price' => 'required|numeric',
            '*.display_order' => 'required|numeric',
            '*.is_available' => 'required|boolean',
        ];

        $sizeValidator = Validator::make($request->sizes, $sizeRules, [], [
            '*.size' => 'Product Sizes - Size',
            '*.price' => 'Product Sizes - Price',
            '*.display_order' => 'Product Sizes - Display Order',
            '*.is_available' => 'Product Sizes - Is Available',
        ]);
        $sizeValidator->validate();
    }
}
