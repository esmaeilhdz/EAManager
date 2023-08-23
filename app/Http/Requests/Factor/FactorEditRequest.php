<?php

namespace App\Http\Requests\Factor;

use Illuminate\Foundation\Http\FormRequest;

class FactorEditRequest extends FormRequest
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
            'customer_code' => 'required|string|size:32',
            'factor_no' => 'required|string',
            'has_return_permission' => 'required|numeric:in:0,1',
            'is_credit' => 'required|numeric:in:0,1',
            'status' => 'required|numeric:in:1,2,3',
            'settlement_date' => 'required|date',
            'final_price' => 'required|numeric',
            'description' => 'nullable|string',
            'products' => 'required|array',
            'products.*.id' => 'required|numeric|min:1',
            'products.*.product_warehouse_id' => 'required|numeric|min:1',
            'products.*.free_size_count' => 'nullable|numeric|min:0',
            'products.*.size1_count' => 'nullable|numeric|min:0',
            'products.*.size2_count' => 'nullable|numeric|min:0',
            'products.*.size3_count' => 'nullable|numeric|min:0',
            'products.*.size4_count' => 'nullable|numeric|min:0',
            'products.*.price' => 'required|numeric|min:0',
            'payments' => 'required|array',
            'payments.*.id' => 'required|numeric|min:1',
            'payments.*.payment_type_id' => 'required|numeric|min:1',
            'payments.*.price' => 'required|numeric|min:1',
            'payments.*.description' => 'nullable|string',
        ];
    }
}
