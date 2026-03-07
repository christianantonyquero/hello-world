<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\StorePageHotspot;

class UpdateHotspot extends FormRequest
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
        $rules = [
            'name' => 'required|max:250',
            'slug' => 'nullable|max:250|string',
            'type' => [
                'required', 
                'max:250',
                Rule::in(StorePageHotspot::HOTSPOT_TYPES())
            ],
            'x' => 'required|numeric|min:0|max:100',
            'y' => 'required|numeric|min:0|max:100',
            'display_order' => 'required|integer|gte:1',
            'value' => '',
            'video' => 'nullable|url',
        ];

        $type = $this->input('type');
        $value = $this->input('value', null);

        if( !in_array($type, ['HOME', 'PHOTO-BOOTH']) ) {
            // Type is only required for some hotspots
            $rules['value'] = [
                'required', 
                'max:250'
            ];
        }
        if( in_array($type, ['PRODUCT_CATEGORY', 'PRODUCT']) ) {
            array_push($rules['value'], 'integer');
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function($validator){
            $type = $this->input('type');
            $value = $this->input('value', null);

            /*
            if($type == 'PRODUCT') {
                // For PRODUCT Hotspots make sure the value is JSON array with the correct keys
                $json_value = json_decode($value, true);
                $err_msg = 'Invalid HotSpot Value. PRODUCT HotSpots values are JSON';
                if( is_array($json_value) ) {
                    if(!isset($json_value['product_id']) ||
                       !isset($json_value['category_id'])) {
                           $validator->errors()->add('value', $err_msg);
                       }
                } else {
                    $validator->errors()->add('value', $err_msg);
                }
            }*/
            if($type == 'URL') {
                if(!filter_var($value, FILTER_VALIDATE_URL)) {
                    $err_msg = 'Invalid HotSpot Value. URL HotSpots must contain a valid URL.';
                    $validator->errors()->add('value', $err_msg);
                }
            }
        });
    }
}
