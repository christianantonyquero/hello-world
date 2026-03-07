<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;

class AdminUserTest extends DuskTestCase
{
    use WithFaker, DatabaseMigrations;

    public function setUp() :void
    {
        parent::setUp();

        $this->user = factory(User::class)->state('admin')->create();
        $this->nonAdminUser = factory(User::class)->create();
    }

    public function test_admin_users_index_can_be_viewed()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/admin/users')
                    ->assertSee($this->user->name)
                    ->assertSee($this->nonAdminUser->name);

            $browser->with('table', function ($table) {
            });
        });
    }

    public function test_user_can_be_created()
    {
        $name = Str::random(10);
        $email = $name.'@gmail.com';

        $this->browse(function (Browser $browser) use ($name, $email) {
            $browser->loginAs($this->user)
                    ->visit('/admin/users')
                    ->click('.add-user')
                    ->pause(200)
                    ->assertSee('Create New User')
                    ->type('user_name', $name)
                    ->pause(100)
                    ->type('user_email', $email)
                    ->pause(100)
                    ->type('password', 'password')
                    ->pause(100)
                    ->click('#create-user')
                    ->pause(200)
                    ->assertUrlIs(url('/admin/users'));
        });

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);

        $user = User::where('email', $email)->first();
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_a_user_can_be_edited()
    {
        $user = factory(User::class)->create();
        $newEmail = $this->faker->email;

        $this->browse(function (Browser $browser) use ($user, $newEmail){
            $browser->loginAs($this->user)
                    ->visit(url(route('admin.users.index')))
                    ->click('@user-'.$user->id.'-edit')
                    ->pause(300)
                    ->assertSee('Edit User')
                    ->assertInputValue('edit_user_name', $user->name)
                    ->assertInputValue('edit_user_email', $user->email)
                    ->type('edit_user_name', 'Updated name')
                    ->type('edit_user_email', $newEmail)
                    ->click('#update-user')
                    ->pause(500)
                    ->assertPathBeginsWith('/admin/users')
                    ->assertDontSee($user->name);

            $newUser = $user->fresh();
            $this->assertEquals('Updated name', $newUser->name);
            $this->assertEquals($newEmail, $newUser->email);
            $this->assertEquals($user->password, $newUser->password); // password not updated this time

            // test changing password also, this is optional
            $browser->loginAs($this->user)
                    ->visit(url(route('admin.users.index')))
                    ->click('@user-'.$user->id.'-edit')
                    ->pause(500)
                    ->type('edit_password', 'newpassword') // this is causing issues for some reason
                    ->pause(300)
                    ->click('#update-user')
                    ->pause(500);

            $newUser2 = $user->fresh();

            $this->assertTrue(Hash::check('newpassword', $newUser2->password));
        });
    }

    public function test_user_can_be_deleted()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user){
            $browser->loginAs($this->user)
                    ->visit(url(route('admin.users.index')))
                    ->click('@user-'.$user->id.'-delete')
                    ->acceptDialog()
                    ->pause(500)
                    ->assertPathBeginsWith('/admin/users')
                    ->assertDontSee($user->name);

            $this->assertSoftDeleted('users', [
                'id' => $user->id,
            ]);
        });
    }
}
