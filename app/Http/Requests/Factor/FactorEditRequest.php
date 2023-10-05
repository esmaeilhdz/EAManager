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
            'settlement_date' => 'required|date',
            'returned_at' => 'nullable|date',
            'final_price' => 'required|numeric',
            'status' => 'required|numeric|min:1',
            'description' => 'nullable|string',
        ];
    }
}
