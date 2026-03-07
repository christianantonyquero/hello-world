<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateStorePageRequest extends FormRequest
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
        $page_id = $this->route('id');

        return [
            'name' => 'required|max:250',
            'slug' => ['required', 'max:250', 'regex:/^[a-z0-9-_\.\-\/]*$/',
                        // Make sure the slug is unique to this store 
                        Rule::unique('store_pages')->where(function ($query) use($store) {
                            return $query->where('store_id', $store->id);
                        })->ignore($page_id)],
            'minimum_age' => 'nullable|integer|min:0',
            'html_content' => 'nullable|string',
            'background_video' => 'nullable|url',
            'intro_video' => 'nullable|url',
            'background_image' => 'image|max:20000|dimensions:min_width:1400,min_height:800', // Max 20MB
            'parent_id' => 'nullable|integer|exists:store_pages,id',
            'menu_display_order' => 'required|integer|gte:1',
        ];
    }
    public function messages() 
    {
        return [
            'slug.unique' => 'The Slug must be unique for the store'
        ];
    }
}
