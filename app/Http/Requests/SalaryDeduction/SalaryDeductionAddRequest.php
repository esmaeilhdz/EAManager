<?php

namespace App\Http\Requests\SalaryDeduction;

use Illuminate\Foundation\Http\FormRequest;

class SalaryDeductionAddRequest extends FormRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'salary_id' => $this->salary_id,
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
            'salary_id' => 'required|numeric|min:1',
            'product_id' => 'nullable|numeric|min:1',
            'price' => 'required|numeric',
            'description' => 'nullable|string'
        ];
    }
}
