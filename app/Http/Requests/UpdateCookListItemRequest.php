<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCookListItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'cook_list_id' => 'required|numeric|exists:cook_lists,id',
            'dish_id'      => 'required|numeric|exists:dishes,id',
            'amount'       => 'required|numeric|min:1',
        ];
    }

    public function attributes(): array
    {
        return [
            'cook_list_id' => __('common.cook_list'),
            'dish_id'      => __('common.dish'),
            'amount'       => __('common.servings'),
        ];
    }
}
