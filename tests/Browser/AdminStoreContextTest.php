<?php

namespace Tests\Browser;

use App\User;
use App\Store;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AdminStoreContextTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() :void
    {
        parent::setUp();

        $this->user = factory(User::class)->states('admin')->create();
        $this->store = factory(Store::class)->create();
        $this->notStore = factory(Store::class)->create();
    }

    public function test_user_can_select_a_store_context()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/admin')
                    ->select('store_context_picker', $this->store->id)
                    ->pause(500)
                    ->assertUrlIs(url('/admin/store/'.$this->store->id.'/dashboard'));
        });

        $this->user->refresh();

        $this->assertTrue($this->user->currentStore->is($this->store));

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/admin')
                    ->assertSelected('store_context_picker', $this->store->id);
            $this->assertEquals(url('/admin/store/'.$this->store->id.'/dashboard'), $browser->attribute('@store-dashboard-link', 'href'));
            // $this->assertEquals(url('/admin/store/'.$this->store2->id.'/products'), $browser->attribute('@store-products-link', 'href'));
        });
    }

    public function test_context_should_change_when_dashboard_is_visited()
    {
        $user = factory(User::class)->states('admin')->create();
        $user->currentStore()->associate($this->notStore);
        $user->save();

        $this->browse(function (Browser $browser) use($user){
            $browser->loginAs($user)
                    ->visit('/admin')
                    ->assertSelected('store_context_picker', $this->notStore->id)
                    ->visit('/admin/store/'.$this->store->id.'/dashboard')
                    ->assertSelected('store_context_picker', $this->store->id);
        });
    }
}
