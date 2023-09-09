<?php

namespace App\Http\Requests\Cloth;

use Illuminate\Foundation\Http\FormRequest;

class ClothAddRequest extends FormRequest
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
            'name' => 'required|string',
            'seller_place_id' => 'required|numeric|min:1',
            'warehouse_place_id' => 'required|numeric|min:1',
            'receive_date' => 'required|date',
            'factor_no' => 'required|string',
            'price' => 'required|numeric|min:1',
            'items' => 'required|array',
            'items.*.metre' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:1',
            'items.*.color_id' => 'required|numeric|min:1',
        ];
    }
}
