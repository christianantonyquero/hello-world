<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateStoreSong extends FormRequest
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
        $store = $this->route('store');

        return [
            'track_name' => 'required|max:250',
            'artist_name' => 'required|max:250',
            'album_name' => 'nullable|string|max:250',
            'play_order' => 'required|numeric|min:1|max:100', 
            'track_artwork' => 'nullable|image|max:20000|dimensions:min_width:100,min_height:100,max_width:1000,max_height:1000', // Max 20MB
            'track_audio_file' => 'url', // Max 20MB
        ];
    }
}
