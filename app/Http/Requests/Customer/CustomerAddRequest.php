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
            'parent_id' => 'nullable|numeric|min:1',
            'mobile' => 'required|string|starts_with:09',
            'tel' => 'nullable|string',
            'score' => 'required|numeric|min:0'
        ];
    }
}
