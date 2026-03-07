<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserProfilePhotoRequest;

class UserProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        return view('user-profile.index', [
            'user' => $user
        ]);
    }

    public function update(UpdateUserProfileRequest $request)
    {
        // Update User Profile and Redirect
        $validated = $request->validated();
        $user = $request->user();
        $user->update($validated);
        flash('User Profile Successfuly Updated.')->success();
        return redirect(route('user-profile.index'));
    }

    public function delete(Request $request) {
        $user = Auth::user();
        Auth::logout();
        $user->forceDelete();
        return redirect()->route('login');
    }

    // Profile Image
    public function update_profile_photo(UpdateUserProfilePhotoRequest $request)
    {
        // Get User and save the image
        $user = $request->user();
        $user->updateProfilePhoto($request->file('profile_photo'));
        flash('Your Profile Photo was Updated')->success();
        return back();

    }

    public function delete_profile_photo(Request $request)
    {
        $user = $request->user();
        $user->deleteProfilePhoto();
        flash('Your Profile Photo was Deleted')->success();
        return back();
    }
}
