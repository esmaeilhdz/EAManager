<?php

namespace App\Http\Requests\Bill;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BillAddRequest extends FormRequest
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
            'bill_type_id' => ['required','numeric',
                Rule::exists('enumerations', 'enum_id')->where(function ($query) {
                    return $query->where('category_name', 'bill_type');
                }),
            ],
            'bill_id' => 'required|string',
            'payment_id' => 'required|string',
        ];
    }
}
