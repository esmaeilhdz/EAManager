<?php

namespace App\Http\Requests\PersonCompany;

use Illuminate\Foundation\Http\FormRequest;

class PersonCompanyAddRequest extends FormRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'person_code' => $this->person_code,
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
            'person_code' => 'required|string|size:32',
            'company_code' => 'required|string|size:32',
            'start_work_date' => 'required|date',
            'end_work_date' => 'nullable|date',
            'suggest_salary' => 'required|numeric',
            'daily_income' => 'required|numeric',
            'position' => 'required|string',
        ];
    }
}
