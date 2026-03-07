<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

use App\User;
use App\LinkedSocialAccount;
use App\Services\SocialAccountService;

class SocialLoginController extends Controller
{
    // Redirect helpers for differnt outcomes
    protected function _redirectForError($error_msg='') {
        flash($error_msg)->error();
        return redirect(route('login'));
    }

    // Redirect to Providers Login Page
    public function redirectToProvider($provider)
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch(\Exception $e) {
            // error redirecting to provider
            return $this->_redirectForError("Error contacting Login provider: {$provider}");
        }
    }


    // Handle a succesful callback from the Social Provider
    public function handleProviderCallback(SocialAccountService $socialAcctService, $provider)
    {
        try {
            $oauthUser = Socialite::driver($provider)->user();
        } catch(\Exception $e) {
            // Could not login / get user / error / danger
            return $this->_redirectForError("Error getting user from: {$provider}");
        }

        // Look up a system user for the OAuth
        $user = $socialAcctService->findUserByProvider($oauthUser, $provider);

        if( $user ) {
            // We Found a matching OAuth User
            return $this->loginUser($user);
               
        } else {
            return $this->registerOauthUser($socialAcctService, $oauthUser, $provider);
        }
    }

    protected function registerOauthUser(SocialAccountService $socialAcctService, $oauthUser, $oauthProvider) {
        // No matching OAuth User
        if(Auth::guest()) {
            // If we have a User with the same email as the OAuth User add the OAuth login to their account and log them in
            if($oauthUser->email &&
               $matchingUser = User::where('email', $oauthUser->email)->first()) {
                // Found a user with a matching email, add this OAuth account and login them in
                $matchingUser->addSocialAccount($oauthProvider, $oauthUser->getId());
                auth()->login($matchingUser, true);
                flash('Login Successful')->success();
                return $this->redirectHome();
            } 

            // Register
            // no matching user so register save auth data in session and redirect to register
            $socialAcctService->saveSocialRegisterData($oauthUser, $oauthProvider);
            return redirect(route('register'));

        } else {
            // Add to User's Account. Redirect to user profile
            Auth::user()->addSocialAccount($oauthProvider, $oauthUser->getId());
            flash('New Login Account Successfully Added')->success();
            return redirect(route('user-profile.index'));
        }
    }
    // Login the Oauth User
    protected function loginUser(User $user) {
        if(Auth::guest()) {
            // Guest - Login the User
            auth()->login($user, true);
            flash('Login Successful')->success();
            return $this->redirectHome();
        } else {
            // User is already logged in so don't do anything, 
            // just redirect to the profile page with message
            if( $user->is(Auth::user()) ) {
                flash('The Login Account is Already Assigned To Your User Account')->success();
                return redirect(route('user-profile.index'));
            } else {
                // You are trying to add a social account that is assigned to another user
                // Show error on social accounts page
                flash('This Login Account is assigned to another User')->error();
                return redirect(route('user-profile.index'));
            }
        }
    }

    // Helper to redirect home and remove any FB social URL fragments
    protected function redirectHome() {
        // concat # to the URL to fix FB adding its on URL fragment
        // https://stackoverflow.com/questions/19246072/facebook-login-with-behind-url
        return redirect(RouteServiceProvider::HOME . '#');
    }
}
