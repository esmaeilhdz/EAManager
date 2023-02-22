<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PaymentListRequest extends FormRequest
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
            'page' => 'required|numeric|min:1',
            'per_page' => 'required|numeric',
            'account_id' => 'nullable|numeric|min:1',
            'gate_id' => 'nullable|numeric|min:1',
            'payment_type_id' => 'nullable|numeric|min:1',
        ];
    }
}
