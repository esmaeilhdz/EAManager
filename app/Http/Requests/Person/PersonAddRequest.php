<?php

namespace App\Http\Requests\Person;

use App\Rules\MbMax;
use App\Rules\MbMin;
use Illuminate\Foundation\Http\FormRequest;

class PersonAddRequest extends FormRequest
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
            'name' => ['required', 'string', new MbMin(2), new MbMax(30)],
            'family' => ['required', 'string', new MbMin(5), new MbMax(35)],
            'father_name' => ['required', 'string', new MbMin(3), new MbMax(30)],
            'national_code' => 'required|string|size:10',
            'identity' => 'required|string|max:10',
            'passport_no' => 'nullable|string',
            'score' => 'nullable|numeric|min:0',
        ];
    }
}
