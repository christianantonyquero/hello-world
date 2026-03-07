<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\User;
use App\Store;
use App\Product;
use App\ProductImage;
use App\ProductCategory;

use Illuminate\Support\Str;

class AdminProductTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() :void
    {
        parent::setUp();

        $this->user = factory(User::class)->states('admin')->create();
        $this->store = factory(Store::class)->create();
        $this->category = factory(ProductCategory::class)->create(['store_id' => $this->store->id]);
        $this->category2 = factory(ProductCategory::class)->create(['store_id' => $this->store->id]);
    }

    public function test_can_create_product()
    {
        $name = Str::random(10);

        $this->browse(function (Browser $browser) use ($name) {
            $browser->loginAs($this->user)
                    ->visit('/admin/store/'.$this->store->id.'/products')
                    ->click('.add-product')
                    ->pause(500)
                    ->assertSee('Create New Product')
                    ->type('product_name', $name)
                    ->type('description', 'Product description')
                    ->select('categories', $this->category->id)
                    ->type('size_0_size', 'XXL')
                    ->type('size_0_price', '7.89')
                    ->click('.add-size-option')
                    ->type('size_1_size', 'S')
                    ->type('size_1_price', '5.89')
                    ->attach('images', __DIR__.'/../files/shirt.png')
                    ->click('#create-product')
                    ->pause(500)
                    ->assertPathBeginsWith('/admin/store/'.$this->store->id.'/products')
                    ->assertSee($name);
        });

        $this->assertDatabaseHas('products', [
            'name' => $name,
            'description' => 'Product description',
        ]);

        $dbProduct = Product::where('name', $name)->first();
        $this->assertTrue($dbProduct->categories->contains($this->category));

        $this->assertDatabaseHas('product_sizes', [
            'product_id' => $dbProduct->id,
            'size' => 'XXL',
            'price' => 7.89,
        ]);
        $this->assertDatabaseHas('product_sizes', [
            'product_id' => $dbProduct->id,
            'size' => 'S',
            'price' => 5.89,
        ]);

        $this->assertEquals(1, $dbProduct->images->count());
    }

    public function test_admin_products_index_can_be_viewed()
    {
        $product1 = factory(Product::class)->create(['store_id' => $this->store->id]);
        $product1->addImage('product_images/test.png');
        $product2 = factory(Product::class)->create(['store_id' => $this->store->id]);
        $product2->addImage('product_images/test2.png');

        $this->browse(function (Browser $browser) use ($product1, $product2) {
            $browser->loginAs($this->user)
                    ->visit('/admin/store/'.$this->store->id.'/products')
                    ->assertSee($product1->name)
                    ->assertSee($product2->name);

            $this->assertRegexp('/storage\/product_images\/test.png/', $browser->attribute('@product-'.$product1->id.'-image', 'src'));
            $this->assertRegexp('/storage\/product_images\/test2.png/', $browser->attribute('@product-'.$product2->id.'-image', 'src'));
        });
    }

    public function test_a_product_can_be_edited()
    {
        $product = factory(Product::class)->create(['store_id' => $this->store->id]);
        $product->addSizes([['size' => 'XL', 'price' => '5.11']]);
        $product->addCategories(collect([$this->category]));

        $this->browse(function (Browser $browser) use($product){
            $browser->loginAs($this->user)
                    ->visit(url(route('admin.products.index', $this->store)))
                    ->click('@product-'.$product->id.'-edit')
                    ->pause(200)
                    ->assertSelected('product_edit_categories', $this->category->id)
                    ->assertInputValue('product_edit_size_0_size', 'XL') // see existing size
                    ->assertInputValue('product_edit_size_0_price', '5.11')
                    ->type('product_edit_name', 'Updated name')
                    ->type('product_edit_description', 'Updated description')
                    ->select('product_edit_categories', $this->category2->id)
                    ->type('product_edit_size_0_price', '7.89')
                    ->click('.edit-product-add-size-option')
                    ->type('product_edit_size_1_size', 'S')
                    ->type('product_edit_size_1_price', '5.89')
                    ->click('#update-product')
                    ->pause(500)
                    ->assertPathBeginsWith('/admin/store/'.$this->store->id.'/products');

            $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'name' => 'Updated name',
                'description' => 'Updated description',
            ]);

            $this->assertDatabaseHas('product_sizes', [
                'product_id' => $product->id,
                'size' => 'XL',
                'price' => 7.89,
            ]);

            $this->assertDatabaseHas('product_sizes', [
                'product_id' => $product->id,
                'size' => 'S',
                'price' => 5.89,
            ]);

            $this->assertDatabaseHas('product_product_category', [
                'product_id' => $product->id,
                'product_category_id' => $this->category->id,
            ]);

            $this->assertDatabaseHas('product_product_category', [
                'product_id' => $product->id,
                'product_category_id' => $this->category2->id,
            ]);

        });
    }

    public function test_a_product_can_be_deleted()
    {
        $product = factory(Product::class)->create(['store_id' => $this->store->id]);

        $this->browse(function (Browser $browser) use($product){
            $browser->loginAs($this->user)
                    ->visit(url(route('admin.products.index', $this->store)))
                    ->click('@product-'.$product->id.'-dropdown')
                    ->click('@product-'.$product->id.'-delete')
                    ->acceptDialog()
                    ->pause(500)
                    ->assertPathBeginsWith('/admin/store/'.$this->store->id.'/products')
                    ->assertDontSee($product->name);

            $this->assertSoftDeleted('products', [
                'id' => $product->id,
            ]);
        });
    }
}
