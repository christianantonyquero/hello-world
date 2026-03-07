<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfilePhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'profile_photo' => ['required', 'image', 'max:3000']
        ];
    }

    public function messages() {
        return [
            'profile_photo.max' => 'Your Profile Photo must be less than 3Mb.'
        ];
    }
}
