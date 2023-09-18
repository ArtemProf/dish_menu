<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDishCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'sort'  => 'nullable|numeric|min:2|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'id'    => 'ID',
            'title' => 'Название',
            'sort'  => 'Порядок сортировки',
        ];
    }
}
