<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceEditRequest extends FormRequest
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
            'customer_code' => 'nullable|string|size:32',
            'name' => 'nullable|string',
            'mobile' => 'nullable|string|starts_with:09',
            'final_price' => 'required|numeric',
            'products' => 'required|array',
            'products.*.id' => 'required|numeric|min:1',
            'products.*.product_warehouse_id' => 'required|numeric|min:1',
            'products.*.free_size_count' => 'nullable|numeric|min:0',
            'products.*.size1_count' => 'nullable|numeric|min:0',
            'products.*.size2_count' => 'nullable|numeric|min:0',
            'products.*.size3_count' => 'nullable|numeric|min:0',
            'products.*.size4_count' => 'nullable|numeric|min:0',
            'products.*.price' => 'required|numeric|min:0',
        ];
    }
}
