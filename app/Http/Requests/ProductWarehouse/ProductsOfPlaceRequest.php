<?php

namespace App\Http\Requests\ProductWarehouse;

use Illuminate\Foundation\Http\FormRequest;

class ProductsOfPlaceRequest extends FormRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
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
            'id' => 'required|numeric|min:1',
            'search_txt' => 'nullable|string|min:3|max:50'
        ];
    }
}
