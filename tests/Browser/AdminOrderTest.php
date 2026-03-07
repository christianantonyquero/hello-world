<?php

namespace Tests\Browser;

use App\Order;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AdminOrderTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp(): void {

        parent::setUp();

        $this->scaffold_store();
        $this->scaffold_orders();
    }

    public function test_order_index()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit(url(route('admin.orders')));

            $browser->assertSee($this->orders[0]->id)
                    ->assertSee($this->orders[0]->created_at->format('j M y'))
                    ->assertSee('$6.33');

            $browser->assertSee($this->orders[1]->id)
                    ->assertSee($this->orders[1]->created_at->format('j M y'))
                    ->assertSee('$12.00');

            // don't show unpaid
            $browser->assertDontSee('11 Dec 28')
                    ->assertDontSee('$13.00');
        });
    }

    public function test_single_order_page_404_if_unpaid()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit(url(route('admin.orders.show', $this->orders[2])))
                    ->assertSee('Not Found');
        });
    }

    public function test_single_order_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit(url(route('admin.orders.show', $this->orders[0])));

            $browser->assertSee('Order # '.$this->orders[0]->id);
            $browser->assertSee($this->orders[0]->store->name);
            $browser->assertSee($this->orders[0]->total);
        });
    }
}
