<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

use App\User;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_display_the_login_page()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }
    public function test_login_displays_validation_errors()
    {
        $response = $this->post('/login', []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_valid_user_can_login()
    {
        $user = factory(User::class)->create([
            'password' => Hash::make('password')
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home').'#');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_null_password()
    {
        $user = factory(User::class)->state('admin')->create(['password' => null]);
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => ''
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['password']);
    }
}
