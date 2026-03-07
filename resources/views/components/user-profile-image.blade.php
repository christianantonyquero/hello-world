<figure class="profile-image {{$sizeClass}} user-profile-img" {{$attributes}}>
    @if($profile_image_url)
        <img src="{{ $profile_image_url }}" alt="" />
    @else
        <img src="{{ asset('img/user-admin/base-profile.png') }}" alt="" />
    @endif
</figure>