@if($social_nav_enabled)
    <nav class="social-login-nav">
        <h4 class="is-size-4 mb-2 has-text-centered">{{$title}}</h4>
        <ul class="social-sign-in-links">
            @foreach($social_accounts as $acct)
                <li class="social-acct-{{$acct}}">
                    <a class="button" href="{{ route('auth.socialite.login', ['provider' => $acct]) }}">
                        {{$acct}}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
@endif