<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Store;

class AdminStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // this solution because i need route binding
        $this->withoutMiddleware([
            \App\Http\Middleware\Authenticate::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\AdminAuth::class,
        ]);
    }

    public function test_store_can_be_created()
    {
        Storage::fake('testing');

        $file = UploadedFile::fake()->image('logo.jpg');

        $name = 'Test Store '.Str::random(40);
        $response = $this->post('/admin/store', [
            'tax_exempt' => false,
            'name' => $name,
            'logo' => $file,
            'state_tax_rate' => 1.5,
            'city_tax_rate' => 3.2,
            'county_tax_rate' => 5,
        ]);

        $response->assertStatus(201);

        $this->assertArrayHasKey('admin_url', (array)json_decode($response->content()));
        $this->assertDatabaseHas('stores', [
            'name' => $name,
            'state_tax_rate' => 1.5,
            'city_tax_rate' => 3.2,
            'county_tax_rate' => 5,
        ]);

        Storage::disk('public')->assertExists('logos/'.$file->hashName());
        Storage::disk('public')->assertMissing('missing.jpg');
    }

    public function test_create_store_endpoint_allows_null_taxes()
    {
        $name = 'Test Store '.Str::random(40);
        $response = $this->post('/admin/store', [
            'tax_exempt' => false,
            'name' => $name,
            'logo' => null,
            'state_tax_rate' => null,
            'city_tax_rate' => null,
            'county_tax_rate' => null,
        ]);

        $response->assertStatus(201);

        $this->assertArrayHasKey('admin_url', (array)json_decode($response->content()));
        $this->assertDatabaseHas('stores', [
            'name' => $name,
            'state_tax_rate' => 0,
            'city_tax_rate' => 0,
            'county_tax_rate' => 0,
        ]);
    }

    public function test_store_show_endpoint()
    {
        $store = factory(Store::class)->create();

        $response = $this->get('/admin/store/'.$store->id);

        $response->assertJson($store->toArray());
    }

    public function test_store_can_be_edited()
    {
        Storage::fake('testing');

        $store = factory(Store::class)->create();

        $file = UploadedFile::fake()->image('logo.jpg');

        $name = 'New Store '.Str::random(40);
        $response = $this->post('/admin/store/'.$store->id, [
            'tax_exempt' => false,
            'name' => $name,
            'logo' => $file,
            'state_tax_rate' => 2.5,
            'city_tax_rate' => 4.2,
            'county_tax_rate' => 7,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('stores', [
            'id' => $store->id,
            'name' => $name,
            'state_tax_rate' => 2.5,
            'city_tax_rate' => 4.2,
            'county_tax_rate' => 7,
        ]);

        Storage::disk('public')->assertExists('logos/'.$file->hashName());
        Storage::disk('public')->assertMissing('missing.jpg');
    }
}
