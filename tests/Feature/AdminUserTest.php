<?php

namespace Tests\Feature;

use App\User;
use App\Store;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();

        // this solution because i need route o binding
        $this->withoutMiddleware([
            \App\Http\Middleware\Authenticate::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\AdminAuth::class,
        ]);
    }

    public function test_user_can_be_created()
    {
        $name = Str::random(20);
        $email = Str::random(20);
        $response = $this->post('/admin/users', [
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'is_admin' => true,
        ]);
        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
            'is_admin' => true
        ]);

        $user = User::where('email', $email)->first();

        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_user_get_endpoint()
    {
        $user = factory(User::class)->create();

        $response = $this->get('/admin/users/'.$user->id);

        $response->assertStatus(200);
        $response->assertJson($user->toArray());
    }
}
