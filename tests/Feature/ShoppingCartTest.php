<?php

namespace Tests\Feature;

use Cart;
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
        $response = $this->post('/store/'.$this->store->id.'/api/cart/add', [
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
        $response = $this->post('/store/'.$this->store->id.'/api/cart/add', [
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

public function test_edit_cart_item_endpoint()
    {
        $this->populate_cart_backend();

        $editId = $this->products[1]->id.'-'.$this->products[1]->sizes[0]->id;

        $response = $this->patch('/store/'.$this->store->id.'/api/cart/'.$editId,[
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

        $response = $this->patch('/store/'.$this->store->id.'/api/cart/'.$editId,[
            'quantity' => 0
        ]);

        $response->assertStatus(200);

        $cartItem = Cart::get($editId);
        $this->assertNull($cartItem);
    }


}
