<?php

namespace App\Http\Controllers\Store\Api;

use Cart;
use App\Product;
use App\ProductSize;

use Illuminate\Http\Request;
use App\Http\Controllers\Store\StoreController;

class CartController extends StoreController
{
    // Helpers
    // Create the id to identify what is getting added to the cart
    public static function makeCartItemID(Product $product, ProductSize $size = null)
    {
        if ( $size )
        {
            return $product->id . '-' . $size->id;
        }
        else {
            return $product->id;
        }
    }

    // Uses "relative" quantity and is used by the AddToCart Method
    protected function isProductCartLimitReached(Product $product, $cartItemID, $quantityToAdd)
    {
        if ( !$product->max_cart_quantity) {
            // product does not have a max cart quantity, bail
            return false;
        }

        // Get the Quantity of the item currently in the cart or 0 if it is not in the cart yet
        $cartItem = Cart::get($cartItemID);
        $cartQuantity = $cartItem ? $cartItem->quantity : 0;

        // Is the 'New Cart Quantity Gt than the max
        $newCartQuantity = $cartQuantity + $quantityToAdd;

        if ( $newCartQuantity > $product->max_cart_quantity) {
            return true;
        } else {
            return false;
        }
    }

    // Route Methods
    public function index()
    {
        $this->checkCartStore();

        return [
            'items' => Cart::getContent()->sortBy(function($item) {
                return[ $item->name, $item->id];

            })->map(function($item){

                $size = null;
                if ( isset($item->attributes['size_id']) && $item->attributes['size_id'] != null ) {
                    $size = $item->associatedModel->sizes()->find($item->attributes['size_id'])->size;
                }

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' =>  number_format($item->price, 2),
                    'quantity' => $item->quantity,
                    'image' => $item->associatedModel->thumbnail_url,
                    'subtotal' => number_format($item->getPriceSum(), 2),
                    'size' => $size,
                    'shopify_variant_id' => isset($item->attributes['shopify_variant_id']) ? $item->attributes['shopify_variant_id'] : null,
                    'max_cart_quantity' => isset($item->attributes['max_cart_quantity']) ? $item->attributes['max_cart_quantity'] : null,
                ];
            })->values(),
            'subtotal' => number_format(Cart::getSubTotal(), 2),
        ];
    }

    public function add(Request $request)
    {
        // Fetch the product and/or size being added
        $product = Product::findOrFail($request->product_id);

        $size = null;
        if ( $request->size_id) {
            $size = $product->sizes->where('id', $request->size_id)->first();
        }

        $quantity = $request->quantity;

        //Don't allow unpublished products to be added to the cart
        if ( !$product->is_published ) {
            return response()->json([
                'error' => 'Invalid Product',
                'message' => 'Product Cannot be added to the cart'
            ], 400);
        }

        // Create a unique ID for this item in the cart
        $cartItemID = self::makeCartItemID($product, $size);

        // If the product has a cart limit, make sure we can add it
        if ( $product->max_cart_quantity &&
            $this->isProductCartLimitReached($product, $cartItemID, $quantity) ) {
                return response()->json([
                    'error' => 'Maximum Cart Limit Reached',
                    'message' => "This item has a limited purchase quantity of {$product->max_cart_quantity}"
                ], 400);
        }

        if( $size ) {
            // make sure size has shopify id
            if($size->shopify_variant_id == null) {
                return response()->json([
                    'error' => 'Missing Shopify ID', 
                    'message' => 'Item Missing a Shopify ID'
                ], 404);
            }

            // Add Size to Cart
            Cart::add([
                'id' => $cartItemID,
                'name' => $product->name,
                'price' => $product->price($size),
                'quantity' => $quantity,
                'associatedModel' => $product,
                'attributes' => [
                    'size_id' => $request->size_id,
                    'shopify_variant_id' => $size->shopify_variant_id,
                    'max_cart_quantity' => $product->max_cart_quantity,
                ]
            ]);
        } else {
            // Make sure product has shopify id
            if( $product->shopify_variant_id == null ) {
                return response()->json([
                    'error' => 'Missing Shopify ID', 
                    'message' => 'Item Missing a Shopify ID'
                ], 404);
            }

            // Add Product to Cart
            Cart::add([
                'id' => $cartItemID,
                'name' => $product->name,
                'price' => $product->price(),
                'quantity' => $quantity,
                'associatedModel' => $product,
                'attributes' => [
                    'size_id' => null,
                    'shopify_variant_id' => $product->shopify_variant_id,
                    'max_cart_quantity' => $product->max_cart_quantity,
                ]
            ]);
        }
        return response()->json(['success' => true]);
    }

    // The update sets a total quantity not a relative quantity
    public function update(Request $request)
    {
        $this->checkCartStore();

        $quantity = $request->quantity; // Total Qty
        $cartItemID = $request->cartId;
        $cartItem = Cart::get($cartItemID);
        $product = $cartItem->associatedModel;

        // Quantity of 0 remove item from cart
        if ( $quantity == 0 ) {
            Cart::remove($cartItemID);
            return;
        }

        // Can Product cart qty be updated b/c of cart limit
        if( $product->max_cart_quantity &&
            ($quantity > $product->max_cart_quantity)) {
                return response()->json([
                    'error' => 'Maximum Cart Limit Reached',
                    'message' => "This item has a limited purchase quantity of {$product->max_cart_quantity}"
                ], 400);
        }

        Cart::update($cartItemID, [
            'quantity' => [
                'relative' => false,
                'value' => $quantity,
            ],
        ]);

        return response()->json(['success' => true]);
    }

    public function clear(Request $request)
    {
        // Clear out the shopping cart
        Cart::clear();
        return response()->json(['success' => true]);
    }

    public function totalQuantity(Request $request)
    {
        // Return total quantity of the shopping cart
        return response()->json(['total_quantity' => Cart::getTotalQuantity()]);
    }

    public function suggested()
    {
        // Return total quantity of the shopping cart

        $items = $store->products()->suggested()->get()->load('sizes', 'images');
        return response()->json(['data' => $items]);
    }
}
