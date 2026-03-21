<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\User;
use App\LinkedSocialAccount;

use App\Services\SocialAccountService;
use Mockery;
use App\Providers\RouteServiceProvider;

// Contains Social Login and Manage Social Tests
class SocialLoginControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const PROVIDER_USER_ID_DEFAULT = '123456789';
    const SOCIAL_EMAIL_ADDR_DEFAULT = 'test@example.com';
    const SOCIAL_NAME_DEFAULT = 'Test User';
    const SOCIAL_NICKNAME_DEFAULT = 'Nick-Name';
    const SOCIAL_AVATAR_DEFAULT = 'https://en.gravatar.com/userimage';
    
    protected function setUp(): void
    {
        parent::setUp();

    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_social_login_with_no_user_should_redirect_to_register()
    {
        $this->withoutExceptionHandling();
        $provider_name = 'twitter';

        $this->mockSocialAccountService();
        
        $response = $this->get(route('auth.socialite.callback', ['provider' => $provider_name]));

        // Test it redirects to the correct registration page with session data set
        $response->assertRedirect(route('register'));
        $response->assertSessionHas('provider.provider_name', $provider_name);
        $response->assertSessionHas('provider.provider_id', self::PROVIDER_USER_ID_DEFAULT);
        $response->assertSessionHas('provider.email', self::SOCIAL_EMAIL_ADDR_DEFAULT);
        $response->assertSessionHas('provider.name', self::SOCIAL_NAME_DEFAULT);
        $response->assertSessionHas('provider.nickname', self::SOCIAL_NICKNAME_DEFAULT);
        $response->assertSessionHas('provider.avatar', self::SOCIAL_AVATAR_DEFAULT);
    }

    public function test_social_login_works()
    {
        // Create a user and linked social with the values returned from my mocked social account
        // and test login
        $provider_name = 'twitter';
        $user = factory(User::class)->create([
            'password' => Hash::make('password')
        ]);
        $user->addSocialAccount($provider_name, self::PROVIDER_USER_ID_DEFAULT);

        $this->mockSocialAccountService($provider_id=self::PROVIDER_USER_ID_DEFAULT,
                                        $email=$user->email);
        
        $response = $this->get(route('auth.socialite.callback', ['provider' => $provider_name]));
        $response->assertRedirect(RouteServiceProvider::HOME . '#');
        $this->assertAuthenticatedAs($user);
    }

   

public function test_user_adds_social_account_that_they_already_connected() 
    {
        // This condition should not happen unless something weird or hacky occurred
        $provider_1_name = 'twitter';
        
        $user = factory(User::class)->create([
            'password' => Hash::make('password')
        ]);
        $user->addSocialAccount($provider_1_name, self::PROVIDER_USER_ID_DEFAULT);

        $this->mockSocialAccountService($provider_id=self::PROVIDER_USER_ID_DEFAULT,
                                        $email=$user->email);

        // 'Login' with the same provider again
        $response = $this->actingAs($user)->get(route('auth.socialite.callback', [
            'provider' => $provider_1_name]));

        $response->assertRedirect(route('user-profile.index'));
        $response->assertSessionHas('flash_notification.0.message', 'The Login Account is Already Assigned To Your User Account');
    }

// Mocking
    protected function mockSocialAccountService($provider_id=self::PROVIDER_USER_ID_DEFAULT, 
                                                $email=self::SOCIAL_EMAIL_ADDR_DEFAULT) {

        $mockSocialite = \Mockery::mock('Laravel\Socialite\Contracts\Factory');
        $this->app['Laravel\Socialite\Contracts\Factory'] = $mockSocialite;

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser
            ->shouldReceive('getId')
                ->andReturn($provider_id)
            ->shouldReceive('getName')
                ->andReturn(self::SOCIAL_NAME_DEFAULT)
            ->shouldReceive('getEmail')
                ->andReturn($email)
            ->shouldReceive('getNickname')
                ->andReturn(self::SOCIAL_NICKNAME_DEFAULT)
            ->shouldReceive('getAvatar')
                ->andReturn(self::SOCIAL_AVATAR_DEFAULT);

        $provider = Mockery::mock('Laravel\Socialite\Contract\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        $mockSocialite->shouldReceive('driver')->andReturn($provider);
    }
}
