<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Image;

class Base64Image implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // we say it is an image if intervention can make one
        // from https://stackoverflow.com/questions/39042731/validate-a-base64-decoded-image-in-laravel
        // here for size check
        try {
            Image::make($value);
            return true;
        } catch(\Exception $exp) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute could not be converted to a valid image.';
    }
}
