<?php

namespace App\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
use App\User;
use App\LinkedSocialAccount;
use Illuminate\Support\Str;

// Wrapper Code for handeling Social account and User account logic
class SocialAccountService
{
    public static function OAUTH_LOGIN_ENABLED()
    {
        return config('services.oauth.login_enabled');
    }
    public static function VALID_OAUTH_PROVIDERS()
    {
        return config('services.oauth.providers');
    }
    
    public function findUserByProvider(ProviderUser $providerUser, string $provider)
    {
        $account = LinkedSocialAccount::where([
            ['provider_name', $provider],
            ['provider_id', $providerUser->getId()]
        ])->first();

        $user = $account ? $account->user : null;
        return $user;
    }
    
    public function saveSocialRegisterData(ProviderUser $providerUser, string $provider)
    {
        // Set the Avatar URL to the (origianl file if exsists or the smaller one)
        $avatar_url = $providerUser->avatar_original ?? $providerUser->getAvatar();

        session([
            'provider.provider_name' => $provider,
            'provider.provider_id' => $providerUser->getId(),
            'provider.email' => $providerUser->getEmail(),
            'provider.name' => $providerUser->getName(),
            'provider.nickname' => $providerUser->getNickname(),
            'provider.avatar' => $avatar_url,
            'provider.token' => $providerUser->token,
        ]);
    }
    // Social Registration / Session Helpers
    public function isSocialRegistration()
    {
        return session()->has('provider.provider_id');
    }

    public function getSocialRegistrationData()
    {
        return session()->get('provider', null);
    }

    public function deleteSocialRegistrationData()
    {
        session()->forget([
            'provider.provider_name',
            'provider.provider_id',
            'provider.email',
            'provider.name',
            'provider.nickname',
            'provider.avatar',
            'provider.token']);
    }

    public function getProfilePhotoUrl()  
    {
        $url = session()->get('provider.avatar', null);
        $provider = session()->get('provider.provider_name', null);
        $token = session()->get('provider.token', null);

        if( $url && Str::upper($provider) == 'FACEBOOK' && $token) {
            $url .= "&access_token={$token}";
        }
        
        return $url;
    }

}