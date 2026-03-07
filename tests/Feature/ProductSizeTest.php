<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Store;
use App\Product;

class ProductSizeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = factory(Store::class)->create();
    }

    public function test_product_sizes_can_be_created()
    {
        $newProduct = factory(Product::class)->create(['store_id' => $this->store->id]);

        $newProduct->addSizes([
            ['size' => 'XL', 'price' => 3.33],
            ['size' => 'S', 'price' => 5.55],
        ]);

        $this->assertDatabaseHas('product_sizes', [
            'product_id' => $newProduct->id,
            'size' => 'XL',
            'price' => 3.33,
        ]);

        $this->assertDatabaseHas('product_sizes', [
            'product_id' => $newProduct->id,
            'size' => 'S',
            'price' => 5.55,
        ]);
    }

    public function test_has_size_method()
    {
        $product = factory(Product::class)->create(['store_id' => $this->store->id]);

        $product->addSizes([
            ['size' => 'XL', 'price' => 3.33],
            ['size' => 'S', 'price' => 5.55],
        ]);

        $this->assertTrue($product->hasSize('XL'));
        $this->assertFalse($product->hasSize('M'));
    }
}
