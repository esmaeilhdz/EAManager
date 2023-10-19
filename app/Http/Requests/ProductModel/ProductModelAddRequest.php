<?php

namespace App\Http\Requests\ProductModel;

use Illuminate\Foundation\Http\FormRequest;

class ProductModelAddRequest extends FormRequest
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
            'name' => 'required|string|min:2',
            'pack_count' => 'required|numeric|min:1',
        ];
    }
}
