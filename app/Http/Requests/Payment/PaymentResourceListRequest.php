<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PaymentResourceListRequest extends FormRequest
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
            'page' => 'required|numeric|min:1',
            'per_page' => 'required|numeric',
            'account_id' => 'nullable|numeric|min:1',
            'gate_id' => 'nullable|numeric|min:1',
            'payment_type_id' => 'nullable|numeric|min:1',
        ];
    }
}
