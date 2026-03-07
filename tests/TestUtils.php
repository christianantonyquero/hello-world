<?php

namespace Tests;

use Cart;
use App\User;
use App\Order;
use App\Store;
use App\Product;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\WithFaker;

trait TestUtils
{
    use WithFaker;

    protected function scaffold_store()
    {
        $this->store = factory(Store::class)->create(['state_tax_rate' => 5, 'county_tax_rate' => 1, 'city_tax_rate' => 1.5]);
        $this->user = factory(User::class)->states('admin')->create();
        $this->products = factory(Product::class, 3)->create(['store_id' => $this->store->id]);
        foreach($this->products as $product){
            $product->addSizes([['size' => 'XL', 'price' => rand(3, 12)], ['size' => 'S', 'price' => rand(3, 12)], ['size' => 'M', 'price' => rand(3, 12)]]);
        }
    }

    protected function populate_cart_frontend()
    {
        $this->browse(function (Browser $browser) {

            // add item
            $browser->loginAs($this->user)
                    ->visit('/')
                    ->visit(url(route('store.product', [$this->store, $this->product])))
                    ->radio('size_id', $this->product->sizes[0]->id)
                    ->select('quantity', 2)
                    ->click('@product-'.$this->product->id.'-cart')
                    ->pause(300);

            // add another one
            $browser->visit(url(route('store.product', [$this->store, $this->product2])))
                    ->radio('size_id', $this->product2->sizes[0]->id)
                    ->select('quantity', 3)
                    ->click('@product-'.$this->product2->id.'-cart')
                    ->pause(300);

            $this->cartTotal =  $this->product->price($this->product->sizes[0]->size) * 2 +
                                $this->product2->price($this->product2->sizes[0]->size) * 3;

        });
    }

    protected function populate_cart_backend()
    {
        Cart::add([
            'id' => $this->products[0]->id.'-'.$this->products[0]->sizes[0]->id,
            'name' => $this->products[0]->name,
            'price' => $this->products[0]->price($this->products[0]->sizes[0]->size),
            'quantity' => 2,
            'associatedModel' => $this->products[0],
            'attributes' => [
                'size_id' => $this->products[0]->sizes[0]->id,
            ],
        ]);

        Cart::add([
            'id' => $this->products[1]->id.'-'.$this->products[1]->sizes[0]->id,
            'name' => $this->products[1]->name,
            'price' => $this->products[1]->price($this->products[1]->sizes[0]->size),
            'quantity' => 3,
            'associatedModel' => $this->products[1],
            'attributes' => [
                'size_id' => $this->products[1]->sizes[0]->id,
            ],
        ]);

        $condition = new \Darryldecode\Cart\CartCondition(array(
            'name' => 'Taxes',
            'type' => 'tax',
            'target' => 'total',
            'value' => $this->store->combinedTaxRate().'%',
        ));

        Cart::condition($condition);
    }

    protected function scaffold_orders()
    {
        $this->orders[] = factory(Order::class)->create([
            'total' => 6.33,
            'created_at' => (string)Carbon::now()->addYears(2),
            'status' => Order::STATUS_COMPLETE,
            'store_id' => $this->store->id,
        ]);
        $this->orders[] = factory(Order::class)->create([
            'total' => 12.00,
            'created_at' => (string)Carbon::now()->addYears(2),
            'status' => Order::STATUS_COMPLETE,
            'store_id' => $this->store->id,
        ]);
        $this->orders[] = factory(Order::class)->create([
            'total' => 13.00,
            'created_at' => (string)Carbon::now()->addYears(2),
            'status' => Order::STATUS_UNPAID,
            'store_id' => $this->store->id,
        ]);
        $this->orders[] = factory(Order::class)->create([
            'total' => 88.00,
            'created_at' => (string)Carbon::now()->addYears(2),
            'status' => Order::STATUS_COMPLETE,
            'store_id' => 0,
        ]);

        // add some items
        $this->orders[0]->items()->create([
            'price' => $this->store->products[0]->price($this->store->products[0]->sizes[0]->size),
            'subtotal' => $this->store->products[0]->price($this->store->products[0]->sizes[0]->size) * 3,
            'quantity' => 3,
            'product_id' => $this->store->products[0]->id,
            'size_id' => $this->store->products[0]->sizes[0]->id,
        ]);
        $this->orders[0]->items()->create([
            'price' => $this->store->products[1]->price($this->store->products[1]->sizes[0]->size),
            'subtotal' => $this->store->products[1]->price($this->store->products[1]->sizes[0]->size) * 1,
            'quantity' => 1,
            'product_id' => $this->store->products[0]->id,
            'size_id' => $this->store->products[1]->sizes[0]->id,
        ]);

    }
}
