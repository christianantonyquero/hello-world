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
        Storage::fake('public');

        $file = UploadedFile::fake()->image('logo.jpg');

        $name = 'Test Store '.Str::random(40);
        $slug = 'test-store-'.Str::random(10);
        $response = $this->post('/admin/store', [
            'name' => $name,
            'slug' => $slug,
            'logo' => $file,
        ]);

        $response->assertStatus(201);

        $this->assertArrayHasKey('admin_url', (array)json_decode($response->content()));
        $this->assertDatabaseHas('stores', [
            'name' => $name,
        ]);

        Storage::disk('public')->assertExists('logos/'.$file->hashName());
        Storage::disk('public')->assertMissing('missing.jpg');
    }

    public function test_create_store_endpoint_allows_null_taxes()
    {
        $name = 'Test Store '.Str::random(40);
        $slug = 'test-store-'.Str::random(10);
        $response = $this->post('/admin/store', [
            'name' => $name,
            'slug' => $slug,
            'logo' => null,
        ]);

        $response->assertStatus(201);

        $this->assertArrayHasKey('admin_url', (array)json_decode($response->content()));
        $this->assertDatabaseHas('stores', [
            'name' => $name,
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
        Storage::fake('public');

        $store = factory(Store::class)->create();

        $file = UploadedFile::fake()->image('logo.jpg');

        $name = 'New Store '.Str::random(40);
        $response = $this->post('/admin/store/'.$store->id, [
            'name' => $name,
            'slug' => $store->slug,
            'logo' => $file,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('stores', [
            'id' => $store->id,
            'name' => $name,
        ]);

        Storage::disk('public')->assertExists('logos/'.$file->hashName());
        Storage::disk('public')->assertMissing('missing.jpg');
    }
}
