<?php

namespace Tests\Browser;

use App\User;
use App\Order;
use App\Store;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AdminStoreDashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() :void
    {
        parent::setUp();

        $this->user = factory(User::class)->states('admin')->create();
        $this->store = factory(Store::class)->create();
    }

    public function test_admin_store_dashboard_shows_orders()
    {
        $orders[] = factory(Order::class)->create([
            'total' => 6.33,
            'created_at' => '2030-03-03 23:59:07',
            'status' => Order::STATUS_COMPLETE,
            'store_id' => $this->store->id,
        ]);
        $orders[] = factory(Order::class)->create([
            'total' => 12.00,
            'created_at' => '2027-12-11 23:59:07',
            'status' => Order::STATUS_COMPLETE,
            'store_id' => $this->store->id,
        ]);
        $orders[] = factory(Order::class)->create([
            'total' => 13.00,
            'created_at' => '2028-12-11 23:59:07',
            'status' => Order::STATUS_UNPAID,
            'store_id' => $this->store->id,
        ]);

        $this->browse(function (Browser $browser) use ($orders){
            $browser->loginAs($this->user)
                    ->visit($this->store->admin_url);

            $browser->assertSee($orders[0]->id)
                    ->assertSee('3 Mar 30')
                    ->assertSee('$6.33');

            $browser->assertSee($orders[1]->id)
                    ->assertSee('11 Dec 27')
                    ->assertSee('$12.00');

            // don't show unpaid
            $browser->assertDontSee('11 Dec 28')
                    ->assertDontSee('$13.00');
        });
    }
}
