<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use App\Services\SocialAccountService;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

    }
    protected function showLoginForm(SocialAccountService $socialAcctService)
    {
        // Delete old social registration here, clean up
        $socialAcctService->deleteSocialRegistrationData();
        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        // Add message to redirect
        flash('Login Successful')->success();
        
        // handle URL hash fragments redirects for JS SPA
        $redirectRequest = redirect()->intended($this->redirectTo);
        $redirectRequest->setTargetUrl($redirectRequest->getTargetUrl() . $request->input('urlHash', ''));
        return $redirectRequest->with([
            'status' => 'Login Successful'
        ]);
    }
}
