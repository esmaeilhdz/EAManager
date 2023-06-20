<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserAddRequest extends FormRequest
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
            'person_code' => 'required|string|size:32',
            'email' => 'nullable|email',
            'mobile' => 'required|string|size:11|starts_with:09',
            'password' => 'required|string|confirmed|min:8',
        ];
    }
}
