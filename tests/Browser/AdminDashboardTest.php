<?php

namespace Tests\Browser;

use App\User;
use App\Order;
use App\Store;
use Carbon\Carbon;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AdminDashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() :void
    {
        parent::setUp();

        $this->user = factory(User::class)->states('admin')->create();
        $this->store = factory(Store::class)->create();
    }

    public function test_admin_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/admin')
                    ->assertSee('Dashboard')
                    ->assertSee('Order History');
        });
    }

    public function test_admin_dashboard_shows_orders()
    {
        $orders[] = factory(Order::class)->create([
            'total' => 6.33,
            'created_at' => (string)Carbon::now()->addYears(2),
            'status' => Order::STATUS_COMPLETE,
            'store_id' => $this->store,
        ]);
        $orders[] = factory(Order::class)->create([
            'total' => 12.00,
            'created_at' => (string)Carbon::now()->addYears(2),
            'status' => Order::STATUS_COMPLETE,
            'store_id' => $this->store,
        ]);
        $orders[] = factory(Order::class)->create([
            'total' => 13.00,
            'created_at' => (string)Carbon::now()->addYears(2),
            'status' => Order::STATUS_UNPAID,
            'store_id' => $this->store,
        ]);

        $this->browse(function (Browser $browser) use ($orders){
            $browser->loginAs($this->user)
                    ->visit('/admin');

            $browser->assertSee($orders[0]->id)
                    ->assertSee('$6.33');

            $browser->assertSee($orders[1]->id)
                    ->assertSee('$12.00');

            // don't show unpaid
            $browser->assertDontSee('$13.00');
        });
    }
}
