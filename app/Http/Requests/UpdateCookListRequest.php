<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCookListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title'           => 'required|string|min:3|max:255',
            'is_default'      => 'boolean|nullable',
            'items'           => 'nullable|array',
            'items.*.id'      => 'numeric',
            'items.*.dish_id' => 'required|exists:dishes,id',
            'items.*.amount'  => 'required|numeric|min:1',
        ];
    }

    public function attributes()
    {
        return [
            'title'           => __('common.title'),
            'is_default'      => __('common.list_by_default'),
            'items'           => __('common.cook_list_dish'),
            'items.*.id'      => __('common.id'),
            'items.*.dish_id' => __('common.dish'),
            'items.*.amount'  => __('common.amount'),
        ];
    }
}
