<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\User;

class ChangePasswordControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_see_change_password_page()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get(route('user-profile.password.change'));
        $response->assertOk();
        $response->assertViewIs('auth.passwords.change');
    }

    public function test_guests_cannot_see_change_password_page()
    {
        $response = $this->get(route('user-profile.password.change'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_change_password_with_valid_data()
    {
        $old_password = $this->faker->password(10);
        $new_password = "Str0ngP@ssword!";
        
        $user = factory(User::class)->create(['password' => Hash::make($old_password)]);

        $response = $this->actingAs($user)->patch(route('user-profile.password.update'), [
            'current_password' => $old_password,
            'password' => $new_password,
            'password_confirmation' => $new_password,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('user-profile.index'));
    }

    public function test_user_cannot_change_password_with_invalid_data()
    {
        $old_password = $this->faker->password(10);
        $user = factory(User::class)->create(['password' => Hash::make($old_password)]);

        $response = $this->actingAs($user)->patch(route('user-profile.password.update'), [
            'current_password' => $old_password,
            'password' => 'Password1!',
            'password_confirmation' => 'Nomatch',
        ]);
        $response->assertSessionHasErrors();

        $response = $this->actingAs($user)->patch(route('user-profile.password.update'), [
            'current_password' => $old_password,
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);
        $response->assertSessionHasErrors();

        $response = $this->actingAs($user)->patch(route('user-profile.password.update'), [
            'current_password' => $old_password,
            'password' => '',
            'password_confirmation' => '',
        ]);
        $response->assertSessionHasErrors();

        $response = $this->actingAs($user)->patch(route('user-profile.password.update'), [
            'current_password' => 'wrong',
            'password' => 'P@ssword1',
            'password_confirmation' => 'P@ssword1',
        ]);
        $response->assertSessionHasErrors();
    }
}
