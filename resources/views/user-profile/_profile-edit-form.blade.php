<form id="id_user-profile-edit-form" 
      class="user-profile-edit-form" 
      method="POST" action="{{ route('user-profile.update') }}">
    @csrf
    @method('PATCH')

    <h4 class="is-size-4 mb-4 has-text-primary">Update Your Information</h4>

    <div class="field">
        <label for="name" class="label">Name</label>       
        <div class="control">                 
            <input id="name" type="text" class="input @error('name') is-danger @enderror is-medium" name="name" 
                value="{{ old('name', $user->name) }}" required autocomplete="name">

            @error('name')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="field">
        <label class="label" for="date_of_birth">Date of Birth</label>
        <div class="control">
            <input name="date_of_birth" id="date_of_birth" type="date"
                   class="input @error('date_of_birth') is-invalid @enderror is-medium"
                   value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}" />
            
            @error('date_of_birth')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="field">
        <label class="label" for="phone">Phone Number</label>
        <div class="control">
            <input name="phone" id="phone" type="tel"
                placeholder="555-555-1212"
                class="input @error('phone') is-invalid @enderror is-medium"
                value="{{ old('phone', $user->phone) }}" />
    
            @error('phone')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="field">
        <div class="control">
            <button type="submit" class="button is-primary mt-2">Save</button>
        </div>
    </div>
</form>