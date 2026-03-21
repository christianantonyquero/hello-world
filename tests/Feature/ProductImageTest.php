<?php

namespace Tests\Feature;

use App\Store;
use App\Product;
use Tests\TestCase;
use App\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = factory(Store::class)->create();
        $this->product = factory(Product::class)->create(['store_id' => $this->store->id]);
        $this->product->addImage('product_images/test.png');
        $this->productNoImage = factory(Product::class)->create(['store_id' => $this->store->id]);
    }


}
