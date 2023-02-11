<?php

namespace App\Http\Requests\SalePeriod;

use Illuminate\Foundation\Http\FormRequest;

class SalePeriodEditRequest extends FormRequest
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
            'id' => 'required|numeric|min:1',
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ];
    }
}
