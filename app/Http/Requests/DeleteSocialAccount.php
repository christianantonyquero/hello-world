<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteSocialAccount extends FormRequest
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
        // Make sure the user is connected to the specific social account
        return [
            'social_account' => ['required', 'string', 
                                 Rule::exists('linked_social_accounts', 'provider_name')->where(function($query) {
                                    $query->where('user_id', auth()->user()->id);
                                 })],
        ];
    }

    public function messages() {
        return [
            'social_account.exists' => 'The Social Account does not exist for your User Account.'
        ];
    }
}
