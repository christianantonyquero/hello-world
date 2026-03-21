<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;

use Tests\TestCase;

use App\User;
use App\Mail\UserRegistered;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user_data = [];
    protected $email = '';
    protected $password = '';

    protected function setUp():void
    {
        parent::setUp();

        $this->email = $this->faker->safeEmail;
        $this->password = 'ABCabc123!!!';
        $name = $this->faker->name;

        $this->user_data = [
            'name' => $name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password,
            'display_name' => 'Display Name: ' . $name,
            'date_of_birth' => $this->faker->dateTimeThisCentury->format('Y-m-d'),
            'phone' =>  $this->faker->phoneNumber,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state_province' => $this->faker->stateAbbr,
            'country' => $this->faker->country,
            'postal_code' => $this->faker->postCode,
            'agree_to_terms' => true
        ];
    }

    public function test_can_see_registration_page()
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_auth_users_cant_see_registration_page()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('register'));
        $response->assertRedirect(route('home'));
    }

    public function test_can_user_register() 
    {
        Mail::fake();

        $response = $this->post(route('register'), $this->user_data);

        $response->assertSessionHasNoErrors();

        $user = User::where([
            ['email',  $this->email],
        ])->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);

        // Check fields got set
        $this->assertEquals($user->name, $this->user_data['name']);
        $this->assertEquals($user->phone, $this->user_data['phone']);
        $this->assertEquals($user->display_name, $this->user_data['display_name']);
        $this->assertEquals($user->date_of_birth->format('Y-m-d'), $this->user_data['date_of_birth']);
        $this->assertEquals($user->address, $this->user_data['address']);
        $this->assertEquals($user->city, $this->user_data['city']);
        $this->assertEquals($user->state_province, $this->user_data['state_province']);
        $this->assertEquals($user->country, $this->user_data['country']);


        // Check Email was setn
        Mail::assertSent(function(UserRegistered $mail) use($user) {
            return $mail->hasTo($user->email) && 
                   $mail->user->id === $user->id;
        });

        $response->assertRedirect(route('home'));
    }

    public function test_invalid_user_registration()
    {
        Mail::fake();
        
        $name = $this->faker->name;
        $email = $this->faker->safeEmail;
        $password = $this->faker->password(10);

        $response = $this->post(route('register'), [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertSessionHasErrors();

        $user = User::where([
            ['email', $email],
            ['name', $name],
        ])->first();
        
        $this->assertNull($user);
        Mail::assertNothingSent();

        // TODO: better invalid registration testing
    }
}
