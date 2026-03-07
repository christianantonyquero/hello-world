<?php

namespace Tests\Feature;

use App\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_status_string()
    {
        $order = factory(Order::class)->create();
        $orderUnpaid = factory(Order::class)->create(['status' => Order::STATUS_UNPAID]);
        $orderComplete = factory(Order::class)->create(['status' => Order::STATUS_COMPLETE]);

        $this->assertEquals('-', $order->statusString());
        $this->assertEquals('Unpaid', $orderUnpaid->statusString());
        $this->assertEquals('Complete', $orderComplete->statusString());
    }
}
