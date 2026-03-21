<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\Mail\UserRegistered;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

use App\Services\SocialAccountService;

use App\Rules\PasswordComplexity;

use Session;

use App\Services\KlaviyoService;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $socialAcctService = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SocialAccountService $socialAcctService)
    {
        $this->middleware('guest');
        $this->socialAcctService = $socialAcctService;
    }

    public function redirectPath()
    {
        // redirect to home (default store)
        return route('home');
    }

    // Customize the Registration form to pass possible default value
    public function showRegistrationForm(Request $request)
    {
        $social_registration = null;

        // If we are doing social registration, fetch the data for the form and keep it for post registration
        if( $this->socialAcctService->isSocialRegistration()) {
            $social_registration = $this->socialAcctService->getSocialRegistrationData();            
        }

        $countries = \Countries::getList('en');

        return view('auth.register', [
            'countries' => $countries,
            'social_registration' => $social_registration,
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:190', 'confirmed', new PasswordComplexity],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:250'],
            'profile_photo' => ['nullable', 'image', 'max:3000'],
            'agree_to_terms' => ['accepted'],
            'opt_in_newsletter' => ['boolean'],
            'umusic_email_opt_in' => ['boolean'],
            'umusic_sms_opt_in' => ['boolean'],

        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'display_name' => $data['display_name'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'address_2' => $data['address_2'] ?? null,
            'city' => $data['city'] ?? null,
            'state_province' => $data['state_province'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'country' => $data['country'] ?? null,
            'opt_in_newsletter' => isset($data['opt_in_newsletter']) ? true : false,
            'umusic_email_opt_in' => isset($data['umusic_email_opt_in']) ? true : false,
            'umusic_sms_opt_in' => isset($data['umusic_sms_opt_in']) ? true : false,
        ]);

        // If a social registration connect the account
        if( $this->socialAcctService->isSocialRegistration()) {

            $social_registration = $this->socialAcctService->getSocialRegistrationData();
            // Do I need to do any validation on this
            try {
                $user->addSocialAccount(
                    $social_registration['provider_name'],
                    $social_registration['provider_id']
                );
            }
            catch(\Exception $e) {
                // Error adding registartion data to use account
                flash('Error Adding Social Account Information to User')->error();
            }

            // Try and Fetch the User's Profile Image and save it
            $photo_url = $this->socialAcctService->getProfilePhotoUrl();
            if( $photo_url ) {
                $user->saveRemoteProfilePhoto($photo_url);           
            }
        }

        // Clear out old session data for social registration
        $this->socialAcctService->deleteSocialRegistrationData();
        return $user;
    }

    protected function registered(Request $request, User $user)
    {
        // Post User Registration Activies

        // send registration email to user
        Mail::to($user)->send(new UserRegistered($user));

        // If user opts-in them add them to our Klavyo mailing list
        
        try {
            $klaviyo = new KlaviyoService();
            if( $user->opt_in_newsletter ) {
                $klaviyo->addUserToRegistrationList($user);
            }
            $klaviyo->newUserRegisteredEvent($user);
        }
        catch(Exception $e) {
            // ignore klaviyo exceptions
        }
    }

}
