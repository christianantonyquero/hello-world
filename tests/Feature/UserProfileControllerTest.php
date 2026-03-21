<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\User;

class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    protected $user_data = [];

    protected function setUp():void
    {
        parent::setUp();

        $this->user_data = [
            'name' => 'new name',
            'display_name' => 'display name',
            'date_of_birth' => $this->faker->dateTimeThisCentury->format('Y-m-d'),
            'phone' => '5015551212',
            'address' => 'new address',
            'city' => 'city',
            'state_province' => 'state',
            'postal_code' => 'postal',
            'country' => 'newcountry',
        ];
    }

    public function test_guest_user_cannot_see_profile_page()
    {
        $response = $this->get(route('user-profile.index'));
        $response->assertRedirect(route('login'));
    }
    public function test_auth_user_can_see_profile_page()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get(route('user-profile.index'));
        $response->assertOk();
        $response->assertViewIs('user-profile.index');
        $response->assertSee($user->email);
    }

    public function test_auth_user_can_update_profile_data()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->patch(route('user-profile.update'), 
            $this->user_data);
        $response->assertSessionHasNoErrors();
        $user->refresh();

        $this->assertEquals($user->name, $this->user_data['name']);
        $this->assertEquals($user->phone, $this->user_data['phone']);
        $this->assertEquals($user->display_name, $this->user_data['display_name']);
        $this->assertEquals($user->date_of_birth->format('Y-m-d'), $this->user_data['date_of_birth']);
        $this->assertEquals($user->address, $this->user_data['address']);
        $this->assertEquals($user->city, $this->user_data['city']);
        $this->assertEquals($user->state_province, $this->user_data['state_province']);
        $this->assertEquals($user->country, $this->user_data['country']);

        $response->assertRedirect(route('user-profile.index'));
        
    }
    public function test_auth_user_cannot_update_profile_with_invalid_data()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->patch(route('user-profile.update'), [
        ]);
        $response->assertSessionHasErrors();
    }

    public function test_user_cannot_update_email()
    {
        $og_email = $this->faker->unique()->safeEmail;
        $new_email = $this->faker->unique()->safeEmail;

        $user = factory(User::class)->create(['email' => $og_email]);
        $this->user_data['email'] = $new_email;

        $response = $this->actingAs($user)->patch(route('user-profile.update'), 
            $this->user_data);
        $response->assertSessionHasNoErrors();
        $user->refresh();

        // Test email did not change
        $this->assertEquals($user->email, $og_email);
        $this->assertNotEquals($user->email, $new_email);

        // Test other fields did change
        $this->assertEquals($user->name, $this->user_data['name']);
        $this->assertEquals($user->phone, $this->user_data['phone']);
        

        $response->assertRedirect(route('user-profile.index'));
    }

    public function test_user_can_upload_profile_photo()
    {
        $user_profile_page =route('user-profile.index');

        $user = factory(User::class)->create();

        Storage::fake('public');
        $test_file = UploadedFile::fake()->image('test-avatar.jpg');

        $response = $this->actingAs($user)->from($user_profile_page)->patch(route('user-profile.profile_photo.update'),[
            'profile_photo' => $test_file
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect($user_profile_page);

        $user->fresh();

        Storage::disk('public')->assertExists($user->profile_photo);
    }

    public function test_profile_photo_validation()
    {
        $user_profile_page =route('user-profile.index');

        $user = factory(User::class)->create();

        Storage::fake('public');
        

        $response = $this->actingAs($user)->from($user_profile_page)->patch(route('user-profile.profile_photo.update'),[
            'profile_photo' => ''
        ]);
        // Missing File
        $response->assertSessionHasErrors();
    
        $test_file = UploadedFile::fake()->image('test-avatar.jpg')->size(10000);
        $response = $this->actingAs($user)->from($user_profile_page)->patch(route('user-profile.profile_photo.update'),[
            'profile_photo' => $test_file
        ]);
        // File to large
        $response->assertSessionHasErrors();
    }

    public function test_user_can_delete_photo() 
    {
        $user_profile_page = route('user-profile.index');

        $user = factory(User::class)->create();

        Storage::fake('public');
        $test_file = UploadedFile::fake()->image('test-avatar.jpg');

        $response = $this->actingAs($user)->from($user_profile_page)->patch(route('user-profile.profile_photo.update'),[
            'profile_photo' => $test_file
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect($user_profile_page);

        $user->fresh();

        Storage::disk('public')->assertExists($user->profile_photo);

        $response = $this->actingAs($user)->from($user_profile_page)->delete(route('user-profile.profile_photo.delete'));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect($user_profile_page);

        Storage::disk('public')->assertMissing($user->profile_photo);

        $user->fresh();
        $this->assertNull($user->profile_photo);
    }

    public function test_guests_cannot_upload_or_delete_profile_photos()
    {
        Storage::fake('public');
        $test_file = UploadedFile::fake()->image('test-avatar.jpg');

        $response = $this->patch(route('user-profile.profile_photo.update'),[
            'profile_photo' => $test_file
        ]);

        $response->assertRedirect(route('login'));
        Storage::disk('public')->assertMissing(User::PROFILE_IMG_DIR . '/' . $test_file->hashName());


        $response = $this->delete(route('user-profile.profile_photo.delete'));
        $response->assertRedirect(route('login'));
    }
    
}
