<?php

namespace Tests\Browser;

use Cart;
use App\Store;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ShoppingCartTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() :void
    {
        parent::setUp();

        $this->scaffold_store();
        $this->store2 = factory(Store::class)->create();
    }

    public function test_checkout_not_shown_if_cart_is_empty()
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->user)
                ->visit(url(route('store.checkout', [$this->store])))
                ->pause(300)
                ->assertUrlIs(url(route('store', [$this->store])))
                ->assertSee('Your cart is empty');

        });
    }

    public function test_cart_frontend()
    {
        $product = $this->products[0];
        $product2 = $this->products[1];

        $this->browse(function (Browser $browser) use($product, $product2) {

            // add item
            $browser->loginAs($this->user)
                    ->visit(url(route('store.product', [$this->store, $product])))
                    ->radio('size_id', $product->sizes[0]->id)
                    ->select('quantity', 2)
                    ->click('@product-'.$product->id.'-cart')
                    ->pause(300)
                    ->assertUrlIs(url(route('store.product', [$this->store, $product])))
                    ->assertSee('Product added to cart')
                    ->assertSeeIn('@cart-count', 2); // see count on page

            // add another one
            $browser->visit(url(route('store.product', [$this->store, $product2])))
                    ->radio('size_id', $product2->sizes[0]->id)
                    ->select('quantity', 3)
                    ->click('@product-'.$product2->id.'-cart')
                    ->pause(300)
                    ->assertUrlIs(url(route('store.product', [$this->store, $product2])))
                    ->assertSee('Product added to cart')
                    ->assertSeeIn('@cart-count', 5); // see count on page

            // see cart
            $browser->click('.open-cart')
                    ->pause(300)
                    ->assertSee('Shopping Cart')
                    ->with('table', function ($table) use($product, $product2) {
                        // see products
                        $table->assertSee($product->sizes[0]->size)
                              ->assertSee($product->name)
                              ->assertSee(2)
                              ->assertSee($product->price($product->sizes[0]->size) * 2); // total for product

                        $table->assertSee($product2->sizes[0]->size)
                            ->assertSee($product2->name)
                            ->assertSee(3)
                            ->assertSee($product2->price($product2->sizes[0]->size) * 3); // total for product

                        $subtotal = $product->price($product->sizes[0]->size) * 2 +
                                    $product2->price($product2->sizes[0]->size) * 3;

                        // see subtotal
                        $table->assertSee(number_format($subtotal, 2));

                        // see taxes
                        $table->assertSee(number_format($subtotal * .075, 2));

                        // see total
                        $table->assertSee(number_format($subtotal + $subtotal * .075, 2));

                    });

            // cart should be wiped when switching stores
            $browser->visit(url(route('store', $this->store2)))
                    ->assertSee($this->store2->name)
                    ->assertMissing('@cart-count');
        });
    }

}
