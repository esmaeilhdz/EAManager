<?php

namespace App\Http\Requests\ProductWarehouse;

use Illuminate\Foundation\Http\FormRequest;

class ProductWarehouseComboRequest extends FormRequest
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
        return [
            'search_txt' => 'nullable|string|min:2'
        ];
    }
}