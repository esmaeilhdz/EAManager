<?php

namespace App\Http\Requests\Sewing;

use Illuminate\Foundation\Http\FormRequest;

class SewingAddRequest extends FormRequest
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
            'seamstress_person_id' => 'nullable|numeric|min:1',
            'place_id' => 'nullable|numeric|min:1',
            'is_mozdi_dooz' => 'required|numeric|in:0,1',
            'color_id' => 'required|numeric|min:1',
            'count' => 'required|numeric|min:1',
            'description' => 'required|string',
        ];
    }
}
