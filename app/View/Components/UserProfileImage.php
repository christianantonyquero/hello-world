<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class UserProfileImage extends Component
{
    public $profile_image = null;
    public $profile_image_url = null;
    public $template = null;
    public $sizeClass = "";

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($user=null, $template="small", $sizeClass="")
    {
        if( !$user ) {
            $user = Auth::user();
        }

        $this->profile_image = $user->profile_photo;
        $this->profile_image_url = $user->profile_photo_url;
        $this->template = $template;
        $this->sizeClass = $sizeClass;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.user-profile-image');
    }
}
