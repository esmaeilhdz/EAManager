<?php

namespace App\Http\Requests\ProductToStore;

use Illuminate\Foundation\Http\FormRequest;

class ProductToStoreEditRequest extends FormRequest
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
            'id' => 'required|numeric|min:1',
            'free_size_count' => 'required|numeric|min:0',
            'size1_count' => 'required|numeric|min:0',
            'size2_count' => 'required|numeric|min:0',
            'size3_count' => 'required|numeric|min:0',
            'size4_count' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }
}
