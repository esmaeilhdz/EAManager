<?php

namespace App\Http\Requests\FactorItem;

use Illuminate\Foundation\Http\FormRequest;

class FactorItemAddRequest extends FormRequest
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
            'item_type' => 'required|string|in:accessory,product,cloth',
            'item_id' => 'required',
            'pack_count' => 'nullable|numeric|min:1',
            'metre' => 'nullable|numeric|min:1',
            'count' => 'nullable|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'discount_type_id' => 'nullable|numeric|min:1',
            'discount' => 'nullable|numeric|min:1',
        ];
    }
}
