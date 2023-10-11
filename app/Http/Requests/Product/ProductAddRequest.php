<?php

namespace App\Http\Requests\Product;

use App\Rules\MbMax;
use App\Rules\MbMin;
use Illuminate\Foundation\Http\FormRequest;

class ProductAddRequest extends FormRequest
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
            'cloth_code' => 'required|string|size:32',
            'sale_period_id' => 'required|numeric|min:1',
            'name' => ['required', 'string', new MbMin(2), new MbMax(100)],
        ];
    }
}
