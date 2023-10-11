<?php

namespace App\Http\Requests\ProductAccessory;

use Illuminate\Foundation\Http\FormRequest;

class ProductAccessoryAddRequest extends FormRequest
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
            'cloth_code' => 'nullable|string|size:32',
            'accessory_id' => 'nullable|numeric|min:1',
            'amount' => 'required|numeric',
        ];
    }
}
