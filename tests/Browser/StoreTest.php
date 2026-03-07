<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\User;
use App\Store;
use App\Product;

class StoreTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() :void
    {
        parent::setUp();

        $this->scaffold_store();
    }

    public function test_store_home_page()
    {
        $this->browse(function (Browser $browser) {
            // basic
            $browser->loginAs($this->user)
                ->visit('/store/'.$this->store->id)
                ->assertSee($this->store->name);

            // see products
            foreach($this->products as $product)
            {
                $browser->assertSee($product->name);
                $this->assertEquals($product->thumbnailUrl(), $browser->attribute('@product-'.$product->id.'-image', 'src'));
                $browser->assertSee($product->price());
            }

            // click a product
            $browser->clickLink($this->products[0]->name)
                    ->pause(300)
                    ->assertPathIs('/store/'.$this->store->id.'/product/'.$this->products[0]->id)
                    ->assertSee($this->products[0]->name);
        });
    }

    public function test_store_product_page()
    {
        // need to build out a pretty complex product to test all the little widgets
        $product = factory(Product::class)->create(['store_id' => $this->store->id]);
        $image1 = $product->addImage('product_images/test.png');
        $image2 = $product->addImage('product_images/test2.png');
        $product->addSizes([
            ['size' => 'XS', 'price' => 9.99],
            ['size' => 'XL', 'price' => 10.99],
        ]);

        $this->browse(function (Browser $browser) use($product){

            $browser->loginAs($this->user)
                ->visit('/store/'.$this->store->id.'/product/'.$product->id)
                ->assertSee($product->name)
                ->assertSee($product->description)
                ->assertSee('XS')
                ->assertSee('XL');

                // see images
                foreach($product->images() as $image)
                {
                    $this->assertEquals($image->url(), $browser->attribute('@image-'.$image->id, 'src'));
                }

                // see different prices when switching between sizes
                $this->assertEquals('9.99', $browser->text('#size-price'));
                $browser->radio('size_id', $product->sizes[1]->id);
                $this->assertEquals('10.99', $browser->text('#size-price'));
        });
    }
}
