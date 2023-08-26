<?php

namespace App\Http\Requests\FactorPayment;

use Illuminate\Foundation\Http\FormRequest;

class FactorPaymentAddRequest extends FormRequest
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
            'product_warehouse_id' => 'required|numeric|min:1',
            'free_size_count' => 'required|numeric|min:1',
            'size1_count' => 'required|numeric|min:1',
            'size2_count' => 'required|numeric|min:1',
            'size3_count' => 'required|numeric|min:1',
            'size4_count' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
        ];
    }
}
