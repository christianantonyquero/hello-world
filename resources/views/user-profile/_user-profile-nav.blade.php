<nav class="user-profile-nav">
    <ul>
        <li><a class="{{ (Route::currentRouteName() == 'user-profile.index') ? 'active' : '' }}"
                href="{{ route('user-profile.index') }}">
                <x-icon-frontend.user class="icon"/> <span>User Profile</span>
                </a>
        </li>

     </ul>
</nav>
