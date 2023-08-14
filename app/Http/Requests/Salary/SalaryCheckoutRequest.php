<?php

namespace App\Http\Requests\Salary;

use Illuminate\Foundation\Http\FormRequest;

class SalaryCheckoutRequest extends FormRequest
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
            'account_id' => 'required|numeric|min:1',
            'payment_type_id' => 'required|numeric|min:1',
            'gate_id' => 'nullable|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_tracking_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ];
    }
}
