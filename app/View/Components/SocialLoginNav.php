<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Services\SocialAccountService;

class SocialLoginNav extends Component
{
    public $social_accounts = null;
    public $social_nav_enabled = false;
    public $title ;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title="Sign In With…")
    {
        $this->title = $title;
        $this->social_accounts = SocialAccountService::VALID_OAUTH_PROVIDERS();
        $this->social_nav_enabled = SocialAccountService::OAUTH_LOGIN_ENABLED();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.social-login-nav');
    }
}
