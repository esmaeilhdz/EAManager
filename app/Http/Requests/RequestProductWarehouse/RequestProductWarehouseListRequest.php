<?php

namespace App\Http\Requests\RequestProductWarehouse;

use Illuminate\Foundation\Http\FormRequest;

class RequestProductWarehouseListRequest extends FormRequest
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
            'warehouse_id' => $this->warehouse_id,
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
            'warehouse_id' => 'required|numeric|min:1',
            'page' => 'required|numeric|min:1',
            'per_page' => 'required|numeric',
            'search_txt' => 'nullable|string'
        ];
    }
}
