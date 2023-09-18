<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiAuthLoginUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'password' => 'required|string|min:8|max:50',
        ];
    }

    public function attributes(): array
    {
        return [
            'email'    => __('common.email'),
            'password' => __('common.password'),
        ];
    }
}
