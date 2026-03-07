<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Store;
use App\Product;
use App\ProductCategory;

class ProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->store = factory(Store::class)->create();
        $this->category1 = factory(ProductCategory::class)->create(['store_id' => $this->store->id]);
        $this->category2 = factory(ProductCategory::class)->create(['store_id' => $this->store->id]);
        $this->category3 = factory(ProductCategory::class)->create(['store_id' => $this->store->id]);
    }

    public function test_categories_can_be_added_to_product()
    {
        $newProduct = factory(Product::class)->create(['store_id' => $this->store->id]);

        $newProduct->addCategories(collect([$this->category1, $this->category2]));

        $this->assertTrue($newProduct->categories->contains($this->category1));
        $this->assertTrue($newProduct->categories->contains($this->category2));
        $this->assertFalse($newProduct->categories->contains($this->category3));

        $this->assertTrue($this->category1->products->contains($newProduct));
    }
}
