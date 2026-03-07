<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\User;
use App\Store;
use Illuminate\Support\Str;

class AdminStoreTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() :void
    {
        parent::setUp();

        $this->user = factory(User::class)->states('admin')->create();
        $this->store = factory(Store::class)->create();
        $this->store2 = factory(Store::class)->create();
    }

    public function test_admin_store_index()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/admin/stores')
                    ->assertSee($this->store->name)
                    ->assertSee($this->store2->name);
        });
    }

    public function test_can_create_store()
    {
        $name = Str::random(10);

        $this->browse(function (Browser $browser) use ($name) {
            $browser->loginAs($this->user)
                    ->visit('/admin')
                    ->click('.add-store')
                    ->pause(500)
                    ->assertSee('Create New Store')
                    ->type('name', $name)
                    ->attach('logo', __DIR__.'/../files/logo.png')
                    ->type('state_tax_rate', 6.2)
                    ->type('city_tax_rate', 11.1)
                    ->type('county_tax_rate', 7.7)
                    ->click('#create-store')
                    ->pause(500)
                    ->assertPathBeginsWith('/admin/store')
                    ->assertSee($name);

            $this->assertDatabaseHas('stores', [
                'name' => $name,
                'tax_exempt' => 0,
                'state_tax_rate' => 6.200000,
                'city_tax_rate' => 11.1,
                'county_tax_rate' => 7.7,
            ]);
        });
    }

    public function test_create_store_modal_conditionals()
    {
        $this->browse(function (Browser $browser) {
            // tax exempt
            $browser->loginAs($this->user)
                    ->visit('/admin')
                    ->click('.add-store')
                    ->pause(500)
                    ->assertSee('STATE RATE')
                    ->click('#tax_exempt_label')
                    ->assertDontSee('STATE RATE')
                    ->assertDontSee('CITY RATE')
                    ->assertDontSee('COUNTY RATE')
                    ->click('#tax_exempt_label')
                    ->assertSee('STATE RATE');
        });
    }

    public function test_can_edit_store()
    {
        $name = Str::random(10);
        $store = factory(Store::class)->create();

        $this->browse(function (Browser $browser) use ($name, $store) {
            $browser->loginAs($this->user)
                    ->visit('/admin/stores')
                    ->click('@store-'.$store->id.'-dropdown')
                    ->click('@store-'.$store->id.'-edit')
                    ->pause(500)
                    ->assertSee('Edit Store')
                    ->type('edit_name', $name)
                    ->type('edit_state_tax_rate', 6.2)
                    ->type('edit_city_tax_rate', 11.1)
                    ->type('edit_county_tax_rate', 7.7)
                    ->click('#edit-store')
                    ->pause(500)
                    ->assertPathBeginsWith('/admin/store')
                    ->assertSee($name);

            $this->assertDatabaseHas('stores', [
                'id' => $store->id,
                'name' => $name,
                'tax_exempt' => 0,
                'state_tax_rate' => 6.200000,
                'city_tax_rate' => 11.1,
                'county_tax_rate' => 7.7,
            ]);
        });
    }

    public function test_delete_store()
    {
        $store = factory(Store::class)->create();

        $this->browse(function (Browser $browser) use($store){
            $browser->loginAs($this->user)
                    ->visit(url(route('admin.stores')))
                    ->click('@store-'.$store->id.'-dropdown')
                    ->click('@store-'.$store->id.'-delete')
                    ->acceptDialog()
                    ->pause(500)
                    ->assertPathIs('/admin')
                    ->assertDontSee($store->name);

            $this->assertSoftDeleted('stores', [
                'id' => $store->id,
            ]);
        });
    }

}
