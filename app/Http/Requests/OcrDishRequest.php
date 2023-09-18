<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class OcrDishRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'image' => 'required|mimes:gif,png,jpg,tif,bmp|min:1|max:1024'
        ];
    }

    public function attributes(): array
    {
        return [
            'image' => __('common.image'),
        ];
    }
}
