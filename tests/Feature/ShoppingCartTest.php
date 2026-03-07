<?php

namespace Tests\Feature;

use Cart;
use App\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShoppingCartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // this solution because i need route o binding
        $this->withoutMiddleware([
            \App\Http\Middleware\Authenticate::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\StoreAccess::class,
        ]);

        $this->scaffold_store();
    }

    public function test_add_to_cart_method()
    {
        $response = $this->post('/store/'.$this->store->id.'/cart/add', [
            'product_id' => $this->products[0]->id,
            'size_id' => $this->products[0]->sizes[0]->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(302); //returns redirect back to product page

        $cartId = $this->products[0]->id.'-'.$this->products[0]->sizes[0]->id;
        $cartItem = Cart::get($cartId);

        $this->assertEquals(1, $cartItem->quantity);
        $this->assertEquals($this->products[0]->price($this->products[0]->sizes[0]->size), $cartItem->price);
        $this->assertEquals($this->products[0]->sizes[0]->id, $cartItem->attributes['size_id']);

        // one with a different quantity
        $response = $this->post('/store/'.$this->store->id.'/cart/add', [
            'product_id' => $this->products[1]->id,
            'size_id' => $this->products[1]->sizes[0]->id,
            'quantity' => 3,
        ]);

        $cartId2 = $this->products[1]->id.'-'.$this->products[1]->sizes[0]->id;
        $cartItem2 = Cart::get($cartId2);

        $this->assertEquals(3, $cartItem2->quantity);
        $this->assertEquals($this->products[1]->price($this->products[1]->sizes[0]->size), $cartItem2->price);
        $this->assertEquals($this->products[1]->price($this->products[1]->sizes[0]->size) * 3, $cartItem2->getPriceSum());
        $this->assertEquals($this->products[1]->sizes[0]->id, $cartItem2->attributes['size_id']);

        // check taxes (cart should have a condition with the 7.5 default tax rate)
        $conditions = Cart::getConditions();
        $this->assertEquals(1, $conditions->count());
    }

    public function test_view_cart_endpoint()
    {
        $this->populate_cart_backend();

        $response = $this->get('/store/'.$this->store->id.'/cart');
        $subtotal = $this->products[0]->price($this->products[0]->sizes[0]->size) * 2
                    + $this->products[1]->price($this->products[1]->sizes[0]->size) * 3;

        $response->assertJson([
            'items' => [
                [
                    'id' => $this->products[0]->id.'-'.$this->products[0]->sizes[0]->id,
                    'name' => $this->products[0]->name,
                    'price' => number_format($this->products[0]->price($this->products[0]->sizes[0]->size), 2),
                    'quantity' => 2,
                    'image' => $this->products[0]->thumbnailUrl(),
                    'subtotal' => number_format($this->products[0]->price($this->products[0]->sizes[0]->size) * 2, 2),
                    'size' => $this->products[0]->sizes[0]->size,
                ],
                [
                    'id' => $this->products[1]->id.'-'.$this->products[1]->sizes[0]->id,
                    'name' => $this->products[1]->name,
                    'price' => number_format($this->products[1]->price($this->products[1]->sizes[0]->size), 2),
                    'quantity' => 3,
                    'image' => $this->products[1]->thumbnailUrl(),
                    'subtotal' => number_format($this->products[1]->price($this->products[1]->sizes[0]->size) * 3, 2),
                    'size' => $this->products[1]->sizes[0]->size,
                ],
            ],
            'subtotal' => number_format($subtotal, 2),
            'taxes' => number_format($subtotal * .075, 2),
            'total' => number_format($subtotal + $subtotal * .075, 2),
        ]);
    }

    public function test_edit_cart_item_endpoint()
    {
        $this->populate_cart_backend();

        $editId = $this->products[1]->id.'-'.$this->products[1]->sizes[0]->id;

        $response = $this->patch('/store/'.$this->store->id.'/cart/'.$editId,[
            'quantity' => 7
        ]);

        $response->assertStatus(200);

        $cartItem = Cart::get($editId);
        $this->assertEquals(7, $cartItem->quantity);
    }

    public function test_edit_cart_item_endpoint_set_to_zero()
    {
        $this->populate_cart_backend();

        $editId = $this->products[1]->id.'-'.$this->products[1]->sizes[0]->id;

        $response = $this->patch('/store/'.$this->store->id.'/cart/'.$editId,[
            'quantity' => 0
        ]);

        $response->assertStatus(200);

        $cartItem = Cart::get($editId);
        $this->assertNull($cartItem);
    }

    public function test_order_can_be_created_from_cart()
    {
        $this->populate_cart_backend();

        $order = Order::createFromCurrentCart($this->store);

        $this->assertEquals($this->products[0]->price($this->products[0]->sizes[0]->size) * 2
                 + $this->products[1]->price($this->products[1]->sizes[0]->size) * 3, $order->subtotal);

        $this->assertEquals(1,
            $order->items->where('quantity', 2)
                         ->where('product_id', $this->products[0]->id)
                         ->where('size_id',  $this->products[0]->sizes[0]->id)
                         ->where('price', $this->products[0]->price($this->products[0]->sizes[0]->size))
                         ->where('subtotal', $this->products[0]->price($this->products[0]->sizes[0]->size) * 2)
                         ->count()
                     );

         $this->assertEquals(1,
             $order->items->where('quantity', 3)
                          ->where('product_id', $this->products[1]->id)
                          ->where('size_id',  $this->products[1]->sizes[0]->id)
                          ->where('price', $this->products[1]->price($this->products[1]->sizes[0]->size))
                          ->where('subtotal', $this->products[1]->price($this->products[1]->sizes[0]->size) * 3)
                          ->count()
                      );
    }
}
