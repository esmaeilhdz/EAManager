<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class AddressEditRequest extends FormRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'resource' => $this->resource,
            'resource_id' => $this->resource_id,
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
            'resource' => 'required|string',
            'resource_id' => 'required',
            'id' => 'required|numeric|min:1',
            'province_id' => 'required|numeric|min:1',
            'city_id' => 'required|numeric|min:1',
            'address_kind_id' => 'required|numeric|min:1',
            'address' => 'required|string',
            'tel' => 'required|string',
        ];
    }
}
