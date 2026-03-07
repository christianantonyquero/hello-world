<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Rules\PasswordComplexity;

class ChangePasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Password Change form and update
    public function change_password(Request $request)
    {
        return view('auth.passwords.change');
    }

    public function update_password(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'current_password' => ['required', function($attribute, $value, $fail) use($user) {
                    // Rule for if old password matches
                    if(! Hash::check($value, $user->password)) {
                        return $fail('The current password is incorrect.');
                    }
            }],
            'password' => ['required', 'string', 'min:8', 'max:190', 'confirmed', new PasswordComplexity],
        ]);

        $user->password = Hash::make($validatedData['password']);
        $user->save();

        flash('Your Password was Updated');
        return redirect(route('user-profile.index'));
    }
}
