<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\Store;
use Hash;

class AuthIntendedUrlRedirectTest extends DuskTestCase
{
    use DatabaseMigrations, WithFaker;

    public function setUp() :void
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'password' => Hash::make('password1'),
        ]);
        $this->store = factory(Store::class)->create();
    }

    public function test_user_is_redirected_to_intended_url_after_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('store.line', $this->store))
                    ->assertSee('Login')
                    ->type('email', $this->user->email)
                    ->type('password', 'password1')
                    ->press('Login')
                    ->pause(300)
                    ->assertSee('Line');
        });
    }

    public function test_user_is_redirected_to_intended_url_after_register()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('store.line', $this->store))
                    ->assertSee('Login')
                    ->click('@headerRegister')
                    ->pause(100)
                    ->type('email', $this->faker->email)
                    ->type('password', 'password1')
                    ->type('password_confirmation', 'password1')
                    ->type('name', 'Name')
                    ->type('display_name', 'password')
                    ->type('date_of_birth', '03/03/1997')
                    ->type('phone', '5019448343')
                    ->type('address', '15 main st')
                    ->type('city', 'little rock')
                    ->type('state_province', 'AR')
                    ->type('postal_code', '72211')
                    ->type('country', 'USA')
                    ->check('agree_to_terms')
                    ->press('Create Your Account')
                    ->pause(300)
                    ->assertSee('Line');
        });
    }
}
