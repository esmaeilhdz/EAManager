<?php

namespace App\Http\Requests\Customer;

use App\Rules\MbMax;
use Illuminate\Foundation\Http\FormRequest;

class CustomerAddRequest extends FormRequest
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
            'name' => ['required', 'string', new MbMax(150)],
            'parent_code' => 'nullable|string|size:32',
            'mobile' => 'required|string|starts_with:09',
            'tel' => 'nullable|string',
            'province_id' => 'required|numeric|min:1',
            'city_id' => 'required|numeric|min:1',
            'address_kind_id' => 'required|numeric|min:1',
            'address' => 'required|string',
        ];
    }
}
