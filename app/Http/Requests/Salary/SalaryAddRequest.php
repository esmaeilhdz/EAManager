<?php

namespace App\Http\Requests\Salary;

use Illuminate\Foundation\Http\FormRequest;

class SalaryAddRequest extends FormRequest
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
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'reward_price' => 'required|numeric|min:0',
            'overtime_hour' => 'required|numeric|min:0',
            'salary_deduction' => 'required|numeric|min:0',
            'description' => 'required|string',
        ];
    }
}
