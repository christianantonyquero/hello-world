@extends('layouts.user-profile')

@section('title', 'Profile')

@section('content_user_profile')
<div class="columns is-multiline">
    <div class="column is-full ">
        <div class="profile-intro box">
            <div class="columns">
                <div class="column photo-col is-narrow">
                    @include('user-profile._profile_image_form')
                </div>
                <div class="column name-col">
                    <h5>Hello,</h5>
                    <h2>{{$user->name}}</h2>
                </div>
                <div class="column button-col">
                    @if($user->profile_photo)
                        @includeIf ('user-profile._delete-profile-photo-form')
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="column is-half edit-profile-col">
        <div class="box">
            @include('user-profile._profile-edit-form')
        </div>

    </div>

    <div class="column is-half edit-profile-col">
        <div class="box">
            <div class="box-body">
                <h4 class="is-size-4 mb-4 has-text-primary">Login Info</h4>
                <label class="label mb-0">Email</label>
                <p class="mb-4 mt-0 is-size-3 has-text-weight-light">{{$user->email}}</p>
                <label class="label mb-0">Password:</label>
                <p class="mb-2 mt-0 is-size-3 has-text-weight-light">********</p>
            </div>

            <div class="body-footer">
                <a href="{{ route('user-profile.password.change')}}"
                    class="button is-primary btn-change-password">Change Password</a>

                <form id="form_delete_account" class="user-profile-delete-form" method="POST"
                    action="{{ route('user-profile.delete') }}">
                
                    @csrf
                    @method('DELETE')
                </form>
                <button type="button" id="btn_delete_account"
                        class="button is-primary mt-3 is-outlined">Delete Account</button>
            </div>
        </div>
            
    </div>
</div>

<div class="modal" tabindex="1" role="dialog" id="id_deleteProfilePhotoModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <h5 class="modal-card-title">Delete Profile Photo</h5>
        </header>
        <section class="modal-card-body">
            <p>Are you sure you want to delete your profile photo?</p>
        </section>
        <footer class="modal-card-foot">
                <button type="button" class="button is-danger" id="id_btn_modal_delete">Delete</button>
                <button type="button" class="button is-danger is-outlined" id="id_btn_modal_close">Cancel</button>
        </footer>
    </div>
</div>

<div class="modal" tabindex="1" role="dialog" id="modal_delete_acccount">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <h5 class="modal-card-title">Delete Your Account</h5>
        </header>
        <section class="modal-card-body">
            <h4 class="is-size-4 mb-3">Are you sure you want to delete your delete your account?</h4>
            <p>Deleteing your acccount deletes all your infromation, this action cannot be undone.</p>
        </section>
        <footer class="modal-card-foot">
            <button type="button" class="button is-danger" id="btn_modal_delete_account">Delete</button>
            <button type="button" class="button is-secondary" id="btn_modal_delete_account_close">Cancel</button>
        </footer>
    </div>
</div>


@endsection


@push('extra_js')
    <script>
        // Submit Profile Photo From on Input Change
        document.getElementById('id_profile_image_file').onchange = function(e) {
            document.getElementById('id_user-profile-photo-form').submit();
        };

        var btnDeleteProfilePhoto = document.getElementById('id_btnDeleteProfilePhoto');
        var modal_delete_photo = document.getElementById('id_deleteProfilePhotoModal');
        var form_delete_photo = document.getElementById('id_formDeleteProfilePhoto');
        var btn_modal_delete = document.getElementById('id_btn_modal_delete');

        var modal_delete_account = document.getElementById('modal_delete_acccount');
        var from_delete_account = document.getElementById('form_delete_account');

        function modalOpen(modalEl) {
            modalEl.classList.add('is-active');
        }
        function modalClose(modalEl) {
            modalEl.classList.remove('is-active');
        }
        
        
        // Delete Profile Photo
        // Show the Modal
        if( btnDeleteProfilePhoto ) {
            btnDeleteProfilePhoto.onclick = function(event) {
                event.preventDefault();
                modalOpen(modal_delete_photo);
            };
        }

        // Modal Close
        document.getElementById('id_btn_modal_close').onclick = function(event) {
            event.preventDefault();
            modalClose(modal_delete_photo);
        }

        // Modal Delete Button, submit the form
        btn_modal_delete.onclick = function(event) {
            event.preventDefault();
            form_delete_photo.submit();
        }

        // Delete Account Modal
        document.getElementById('btn_delete_account').onclick = function(event) {
            event.preventDefault();
            modalOpen(modal_delete_account);
        }
        // delete accout modal close
        document.getElementById('btn_modal_delete_account_close').onclick = function(event) {
            event.preventDefault();
            modalClose(modal_delete_account);
        }
        // Delete Account Submit
        document.getElementById('btn_modal_delete_account').onclick = function(event) {
            event.preventDefault();
            from_delete_account.submit()
        }
    </script>
@endpush
