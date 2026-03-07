<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;

class AdminAuthTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    public function test_an_admin_user_can_view_admin()
    {
        $user = factory(User::class)->create(['is_admin' => true]);
        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($user)
                    ->visit('admin')
                    ->assertSee('Dashboard');
        });
    }

    public function test_a_non_admin_user_cannot_view_admin()
    {
        $user = factory(User::class)->create(['is_admin' => false]);
        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($user)
                    ->visit('admin')
                    ->assertDontSee('Dashboard')
                    ->assertPathIs('/');
        });
    }
}
