<?php

namespace App\Http\Requests\Product;

use App\Rules\MbMax;
use App\Rules\MbMin;
use Illuminate\Foundation\Http\FormRequest;

class ProductEditRequest extends FormRequest
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
            'cloth_code' => 'required|string|size:32',
            'sale_period_id' => 'required|numeric|min:1',
            'internal_code' => 'required|string|size:16',
            'name' => ['required', 'string', new MbMin(2), new MbMax(100)],
            'product_accessories' => 'nullable|array',
            'product_accessories.*.accessory_id' => 'nullable|numeric|min:1',
            'product_accessories.*.cloth_code' => 'nullable|string|size:32',
            'product_accessories.*.amount' => 'required|string',
        ];
    }
}
