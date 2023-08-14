<?php

namespace App\Http\Requests\Place;

use App\Rules\MbMax;
use Illuminate\Foundation\Http\FormRequest;

class PlaceEditRequest extends FormRequest
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
            'name' => ['required', 'string', new MbMax(100)],
            'place_kind_id' => 'required|numeric|min:1',
            'department_manager_name' => ['required', 'string', new MbMax(100)],
            'department_manager_national_code' => 'nullable|string|size:10',
            'department_manager_identity' => 'nullable|string',
            'capacity' => 'nullable|numeric|min:1',
            'from_date' => 'required|date',
        ];
    }
}
