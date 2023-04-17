<?php

namespace App\Http\Requests\Enumeration;

use App\Rules\MbMax;
use App\Rules\MbMin;
use Illuminate\Foundation\Http\FormRequest;

class EnumerationAddRequest extends FormRequest
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
            'category_name' => 'required|string',
            'enum_caption' => ['required', 'string', new MbMin(2), new MbMax(100)]
        ];
    }
}
