<form class="user-profile-delete-photo-form" id="id_formDeleteProfilePhoto"
        method="POST" action="{{ route('user-profile.profile_photo.delete') }}">
    @csrf 
    @method('DELETE')
    <button id="id_btnDeleteProfilePhoto" 
            type="submit" class="button is-white btn-delete-photo">Delete Photo</button>
</form>