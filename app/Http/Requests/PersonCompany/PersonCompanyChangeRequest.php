<?php

namespace App\Http\Requests\PersonCompany;

use Illuminate\Foundation\Http\FormRequest;

class PersonCompanyChangeRequest extends FormRequest
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
            'company_code' => $this->company_code,
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
            'is_enable' => 'required|numeric|in:0,1',
        ];
    }
}
