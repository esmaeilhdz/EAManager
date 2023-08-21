<?php

namespace App\Http\Requests\Customer;

use App\Rules\MbMax;
use Illuminate\Foundation\Http\FormRequest;

class CustomerEditRequest extends FormRequest
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
            'name' => ['required', 'string', new MbMax(150)],
            'parent_id' => 'nullable|numeric|min:1',
            'mobile' => 'required|string|starts_with:09',
            'tel' => 'nullable|string',
            'score' => 'required|numeric|min:0',
            'address_id' => 'required|numeric|min:1',
            'province_id' => 'required|numeric|min:1',
            'city_id' => 'required|numeric|min:1',
            'address_kind_id' => 'required|numeric|min:1',
            'address' => 'required|string',
        ];
    }
}
