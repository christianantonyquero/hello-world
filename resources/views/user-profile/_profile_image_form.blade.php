<form id="id_user-profile-photo-form" class="user-profile-update-image-form"
        enctype="multipart/form-data"
        method="POST" action="{{ route('user-profile.profile_photo.update') }}">
    
    @csrf 
    @method('PATCH')
    
    <input  
        id="id_profile_image_file" 
        name="profile_photo"
        class="profile-image-file"
        accept="image/*"
        type="file" required/>

    <label class="label profile-pic-label" for="id_profile_image_file">
        <x-user-profile-image template="medium">
        </x-user-profile-image>
    </label>

    <button type="submit" class="button is-secondary btn-change-photo">Update Photo</a>
</form>

@error('profile_image')
    <p class="invalid-feedback" role="alert">{{ $message }}</p>
@enderror