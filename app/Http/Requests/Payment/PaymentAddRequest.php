<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PaymentAddRequest extends FormRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->id,
            'resource' => $this->resource,
            'resource_id' => $this->resource_id,
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
            'resource' => 'required|string',
            'resource_id' => 'required',
            'account_id' => 'required|numeric|min:1',
            'payment_type_id' => 'required|numeric|min:1',
            'gate_id' => 'nullable|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'payment_date' => 'required|date|before_or_equal:now',
            'payment_tracking_code' => 'required|string',
            'description' => 'required|string',
        ];
    }
}
