<?php

namespace App\Http\Requests\ProductPrice;

use Illuminate\Foundation\Http\FormRequest;

class ProductPriceEditRequest extends FormRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'code' => $this->code,
            'id' => $this->id,
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
            'id' => 'required|numeric|min:1',
            'cutter_person_code' => 'nullable|string|size:32',
            'cutter_place_id' => 'nullable|numeric|min:1',
            'total_count' => 'required|numeric|min:1',
            'serial_count' => 'required|numeric|min:1',
            'sewing_price' => 'required|numeric|min:1',
            'sewing_date' => 'required|date',
            'cutting_price' => 'required|numeric|min:1',
            'cutting_date' => 'required|date',
            'packing_price' => 'required|numeric|min:1',
            'sending_price' => 'required|numeric|min:1',
            'sewing_final_price' => 'required|numeric|min:1',
            'sale_profit_price' => 'required|numeric|min:1',
            'final_price' => 'required|numeric|min:1',
            'product_accessories' => 'required|array',
            'product_accessories.*.product_accessory_id' => 'required|numeric|min:1',
            'product_accessories.*.price' => 'required|numeric|min:1',
        ];
    }
}
