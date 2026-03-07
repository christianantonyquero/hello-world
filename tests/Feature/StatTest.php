<?php

namespace Tests\Feature;

use App\Stat;
use App\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scaffold_store();

        $this->order1 = factory(Order::class)->create(['total' => 3.33, 'status' => Order::STATUS_COMPLETE, 'store_id' => $this->store->id]);
        $this->order2 = factory(Order::class)->create(['total' => 12.51, 'status' => Order::STATUS_COMPLETE, 'store_id' => $this->store->id]);
        $this->order3 = factory(Order::class)->create(['total' => 20.00, 'status' => Order::STATUS_COMPLETE]);
        $this->orderNope = factory(Order::class)->create(['total' => 5.33, 'status' => Order::STATUS_UNPAID]);
    }

    public function test_total_sales_method()
    {
        // all orders
        $this->assertEquals(35.84, Stat::totalSales());

        // single store orders
        $this->assertEquals(15.84, Stat::totalSales($this->store));
        $this->assertEquals(15.84, $this->store->totalSales());
    }

    public function test_total_orders_method()
    {
        // all orders
        $this->assertEquals(3, Stat::totalOrders());

        // single store orders
        $this->assertEquals(2, Stat::totalOrders($this->store));
        $this->assertEquals(2, $this->store->totalOrders());
    }
}
