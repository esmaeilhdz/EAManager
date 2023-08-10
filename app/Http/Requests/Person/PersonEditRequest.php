<?php

namespace App\Http\Requests\Person;

use App\Rules\MbMax;
use App\Rules\MbMin;
use Illuminate\Foundation\Http\FormRequest;

class PersonEditRequest extends FormRequest
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
            'internal_code' => 'required|string|size:12',
            'name' => ['required', 'string', new MbMin(2), new MbMax(30)],
            'family' => ['required', 'string', new MbMin(5), new MbMax(35)],
            'father_name' => ['required', 'string', new MbMin(3), new MbMax(30)],
            'national_code' => 'required|string|size:10',
            'mobile' => 'nullable|string|size:11|starts_with:09',
            'insurance_no' => 'nullable|string',
            'address_id' => 'required|numeric|min:1',
            'province_id' => 'required|numeric|min:1',
            'city_id' => 'required|numeric|min:1',
            'address_kind_id' => 'required|numeric|min:1',
            'tel' => 'nullable|string',
            'address' => 'required|string',
            'identity' => 'required|string|max:10',
            'passport_no' => 'nullable|string',
            'score' => 'required|numeric|min:0',
        ];
    }
}
