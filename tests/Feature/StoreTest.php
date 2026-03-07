<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;
use App\Store;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->states('admin')->create();
    }

    public function test_store_home_page()
    {
        $store = factory(Store::class)->create();

        $response = $this->actingAs($this->user)->get('/store/'.$store->id);

        $response->assertStatus(200);
        $response->assertSee($store->name);
    }

    public function test_store_admin_url()
    {
        $store = factory(Store::class)->create();

        $this->assertStringContainsString('/admin/store/'.$store->id.'/dashboard', $store->admin_url);
    }

    public function test_store_logo_url()
    {
        $store = factory(Store::class)->create(['logo' => 'logos/asdfasdf.jpg']);
        $storeNoLogo = factory(Store::class)->create();

        $this->assertStringContainsString('logos/asdfasdf.jpg', $store->logo_url);
        $this->assertStringContainsString('img/logo-dark.svg', $storeNoLogo->logo_url);
    }

    public function test_store_combined_tax_rate()
    {
        $store1 = factory(Store::class)->create(['state_tax_rate' => 5, 'county_tax_rate' => 1, 'city_tax_rate' => 1.5]);
        $this->assertEquals(7.5, $store1->combinedTaxRate());

        $store2 = factory(Store::class)->create(['state_tax_rate' => 0, 'county_tax_rate' => 1, 'city_tax_rate' => 1.5]);
        $this->assertEquals(2.5, $store2->combinedTaxRate());

        $store3 = factory(Store::class)->create(['state_tax_rate' => 0, 'county_tax_rate' => 0, 'city_tax_rate' => 0]);
        $this->assertEquals(0, $store3->combinedTaxRate());
    }
}
