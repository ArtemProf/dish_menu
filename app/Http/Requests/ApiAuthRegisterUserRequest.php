<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiAuthRegisterUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'nullable|string|min:3|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:50',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => __('common.name'),
            'email'    => __('common.email'),
            'password' => __('common.password'),
        ];
    }
}
