<?php

namespace App\Http\Requests\Factor;

use Illuminate\Foundation\Http\FormRequest;

class FactorAddRequest extends FormRequest
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
            'customer_code' => 'required|string|size:32',
            'sale_period_id' => 'required|numeric|min:1',
            'factor_no' => 'required|string',
            'has_return_permission' => 'required|numeric:in:0,1',
            'is_credit' => 'required|numeric:in:0,1',
            'is_complete' => 'required|numeric:in:0,1',
            'settlement_date' => 'required|date',
            'final_price' => 'required|numeric',
            'description' => 'nullable|string',
            'products' => 'required|array',
            'products.*.product_warehouse_id' => 'required|numeric|min:1',
            'products.*.free_size_count' => 'nullable|numeric|min:0',
            'products.*.size1_count' => 'nullable|numeric|min:0',
            'products.*.size2_count' => 'nullable|numeric|min:0',
            'products.*.size3_count' => 'nullable|numeric|min:0',
            'products.*.size4_count' => 'nullable|numeric|min:0',
            'products.*.price' => 'required|numeric|min:0',
            'payments' => 'required|array',
            'payments.*.payment_type_id' => 'required|numeric|min:1',
            'payments.*.price' => 'required|numeric|min:1',
            'payments.*.description' => 'nullable|string',
        ];
    }
}
