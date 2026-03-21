<?php

namespace Tests\Feature;

use App\Store;
use App\Product;
use App\ProductCategory;

use Gate;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // this solution because i need route o binding
        $this->withoutMiddleware([
            \App\Http\Middleware\Authenticate::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\AdminAuth::class,
        ]);

        $this->store = factory(Store::class)->create();
        $this->category = factory(ProductCategory::class)->create(['store_id' => $this->store->id]);
    }

    public function test_product_can_be_created()
    {
        $file = UploadedFile::fake()->image('logo.jpg');
        $file2 = UploadedFile::fake()->image('logo2.jpg');
        $name = 'Test Product '.Str::random(10);

        $response = $this->post('/admin/store/'.$this->store->id.'/products', [
            'name' => $name,
            'description' => 'some info',
            'categories' => [$this->category->id],
            'sizes' => [
                ['size' => 'XL', 'price' => 6.66]
            ],
            'images' => [$file, $file2]
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'name' => $name,
        ]);

        $dbProduct = Product::where('name', $name)->first();
        $this->assertTrue($dbProduct->categories->contains($this->category));
        $this->assertDatabaseHas('product_sizes', [
            'product_id' => $dbProduct->id,
            'size' => 'XL',
            'price' => 6.66,
        ]);
        $this->assertEquals(2, $dbProduct->images->count());
    }

    public function test_product_show_endpoint()
    {
        $product = factory(Product::class)->create([
            'store_id' => $this->store->id,
        ]);
        $product->addSizes([
            ['size' => 'XL', 'price' => 3.11],
            ['size' => 'S', 'price' => 6.11],
        ]);
        $product->addCategories(collect([$this->category]));
        $product->addImage('product_images/test.png');

        $response = $this->get('/admin/store/'.$this->store->id.'/products/'.$product->id.'/edit');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $product->id,
        ]);
    }
}
