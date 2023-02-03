<?php

namespace App\Http\Requests\AccessoryBuy;

use Illuminate\Foundation\Http\FormRequest;

class AccessoryBuyDetailRequest extends FormRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->id,
            'accessory_id' => $this->accessory_id,
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
            'accessory_id' => 'required|numeric|min:1',
            'id' => 'required|numeric|min:1',
        ];
    }
}
