<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\User;
use App\Store;
use App\Product;
use App\ProductCategory;

use Illuminate\Support\Str;

class AdminProductCategoryTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() :void
    {
        parent::setUp();

        $this->store = factory(Store::class)->create();

        $this->user = factory(User::class)->states('admin')->create();

        $this->category1 = factory(ProductCategory::class)->create(['store_id' => $this->store->id]);
        $this->category2 = factory(ProductCategory::class)->create(['store_id' => $this->store->id]);

        $this->product1 = factory(Product::class)->create(['store_id' => $this->store->id]);
        $this->product2 = factory(Product::class)->create(['store_id' => $this->store->id]);
        $this->product3 = factory(Product::class)->create(['store_id' => $this->store->id]);

        $this->product1->addCategories([$this->category1]);
        $this->product2->addCategories([$this->category1]);
    }

    public function test_product_category_pages()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/admin/store/'.$this->store->id.'/products/categories')
                    ->assertSee($this->category1->name)
                    ->assertSee($this->category2->name)
                    ->clickLink($this->category1->name)
                    ->pause(200)
                    ->assertUrlIs(url('/admin/store/'.$this->store->id.'/products/categories/'.$this->category1->id))
                    ->assertSee($this->product1->name)
                    ->assertSee($this->product2->name)
                    ->assertDontSee($this->product3->name);
        });
    }

    public function test_can_add_product_category()
    {
        $name = Str::random(10);

        $this->browse(function (Browser $browser) use ($name) {
            $browser->loginAs($this->user)
                    ->visit('/admin/store/'.$this->store->id.'/products/categories')
                    ->type('name', $name)
                    ->click('#create-category')
                    ->pause(200)
                    ->assertUrlIs(url('/admin/store/'.$this->store->id.'/products/categories'));

            $this->assertDatabaseHas('product_categories', [
                'name' => $name,
            ]);
        });
    }

    public function test_can_delete_product_category()
    {
        $category = factory(ProductCategory::class)->create(['store_id' => $this->store->id]);

        $this->browse(function (Browser $browser) use ($category) {
            $browser->loginAs($this->user)
                    ->visit('/admin/store/'.$this->store->id.'/products/categories')
                    ->click('@category-'.$category->id.'-delete')
                    ->acceptDialog()
                    ->pause(500)
                    ->assertPathBeginsWith('/admin/store/'.$this->store->id.'/products/categories')
                    ->assertDontSee($category->name);

            $this->assertSoftDeleted('product_categories', [
                'id' => $category->id,
            ]);
        });
    }
}
