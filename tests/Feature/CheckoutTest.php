<?php

namespace Tests\Feature;

use Cart;
use App\User;
use App\Order;
use Carbon\Carbon;
use Tests\TestCase;
use App\Mail\OrderComplete;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutTest extends TestCase
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

        $this->user = factory(User::class)->create();
        $this->scaffold_store();
        $this->product = $this->products[0];
        $this->product2 = $this->products[1];
        $this->populate_cart_backend();
        $this->order = Order::createFromCurrentCart($this->store);
        $this->order->update([
            'user_id' => $this->user->id,
        ]);
    }

    public function test_checkout_endpoint_credit_card()
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->post('/store/'.$this->store->id.'/checkout/'.$this->order->uuid, [
            'token' => 'tok_visa',
        ]);

        $response->assertStatus(200);

        Mail::assertSent(OrderComplete::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });

        $order = $this->order->fresh();

        $this->assertEquals('complete', $order->status);
        $this->assertNotNull('stripe_charge_id');

        // cart should be empty now
        $this->assertEquals(0, Cart::getContent()->count());
    }

    public function test_checkout_endpoint_returns_error_if_already_complete()
    {
        $this->order->update(['status' => Order::STATUS_COMPLETE]);
        $response = $this->post('/store/'.$this->store->id.'/checkout/'.$this->order->uuid, [
            'name' => 'Order Name',
            'payment_type' => 'invoice',
        ]);

        $response->assertStatus(409)
                 ->assertJson(['error' => 'This order has already been completed.']);
    }

    public function test_checkout_view_should_redirect_once_order_is_paid()
    {
        $this->order->update(['status' => Order::STATUS_COMPLETE]);

        $response = $this->get('/store/'.$this->store->id.'/checkout/'.$this->order->uuid);

        $response->assertStatus(302);
    }

    public function test_order_expiry()
    {
        $order1 = Order::createFromCurrentCart($this->store);
        $order2 = Order::createFromCurrentCart($this->store);
        $order2->created_at = Carbon::now()->subHours(2);

        $this->assertFalse($order1->expired());
        $this->assertTrue($order2->expired());
    }

}
