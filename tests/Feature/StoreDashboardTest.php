<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;
use App\Store;

class StoreDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_dashboard_displays_content()
    {
        $user = factory(User::class)->states('admin')->create();
        $store = factory(Store::class)->create();

        $response = $this->actingAs($user)->get('/admin/store/'.$store->id.'/dashboard');

        $response->assertStatus(200);

        $response->assertSee($store->name);
    }

}
