<?php

namespace App\Http\Requests\User;

use App\Traits\Common;
use Illuminate\Foundation\Http\FormRequest;

class UserEditRequest extends FormRequest
{
    use Common;

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'code' => $this->code,
            'mobile' => $this->FaToEn($this->mobile),
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
            'email' => 'nullable|email',
            'mobile' => 'required|string|size:11|starts_with:09',
        ];
    }
}
