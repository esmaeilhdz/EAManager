<?php

namespace App\Http\Requests\Warehouse;

use App\Rules\MbMin;
use Illuminate\Foundation\Http\FormRequest;

class WarehouseComboRequest extends FormRequest
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
            'search_txt' => ['nullable', 'string', new MbMin(2)]
        ];
    }
}
