<?php

namespace App\Http\Requests\ClothBuy;

use Illuminate\Foundation\Http\FormRequest;

class ClothBuyAddRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'code' => $this->code
        ]);
    }

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
            'code' => 'required|string|size:32',
            'seller_place_id' => 'required|numeric|min:1',
            'warehouse_place_id' => 'required|numeric|min:1',
            'receive_date' => 'required|date',
            'price' => 'required|string',
            'factor_no' => 'required|string',
            'items' => 'required|array',
            'items.*.metre' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:1',
            'items.*.color_id' => 'required|numeric|min:1',
        ];
    }
}
