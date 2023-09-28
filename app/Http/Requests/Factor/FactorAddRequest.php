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
            'has_return_permission' => 'required|numeric:in:0,1',
            'is_credit' => 'required|numeric:in:0,1',
            'status' => 'required|numeric:in:1,2,3',
            'settlement_date' => 'required|date',
            'final_price' => 'required|numeric',
            'description' => 'nullable|string',
        ];
    }
}
