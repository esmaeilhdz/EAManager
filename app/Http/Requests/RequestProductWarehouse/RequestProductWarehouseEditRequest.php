<?php

namespace App\Http\Requests\RequestProductWarehouse;

use Illuminate\Foundation\Http\FormRequest;

class RequestProductWarehouseEditRequest extends FormRequest
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
            'id' => $this->id,
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
            'id' => 'required|numeric|min:1',
            'free_size_count' => 'required|numeric|min:1',
            'size1_count' => 'required|numeric|min:1',
            'size2_count' => 'required|numeric|min:1',
            'size3_count' => 'required|numeric|min:1',
            'size4_count' => 'required|numeric|min:1',
        ];
    }
}
