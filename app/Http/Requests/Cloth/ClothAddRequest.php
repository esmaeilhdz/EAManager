<?php

namespace App\Http\Requests\Cloth;

use Illuminate\Foundation\Http\FormRequest;

class ClothAddRequest extends FormRequest
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
            'name' => 'required|string',
            'color_id' => 'required|numeric|min:1'
        ];
    }
}
