<?php

namespace App\Http\Requests\Cutting;

use Illuminate\Foundation\Http\FormRequest;

class CuttingAddRequest extends FormRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'code' => $this->code
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
        $rules = [
            'code' => 'required|string|size:32',
            'cutted_count' => 'required|numeric|min:1',
        ];

        if ($this->request->has('free_size_count')) {
            $rules['free_size_count'] = 'required|numeric|min:1';
        }

        if ($this->request->has('size1_count')) {
            $rules['size1_count'] = 'required|numeric|min:1';
        }

        if ($this->request->has('size2_count')) {
            $rules['size2_count'] = 'required|numeric|min:1';
        }

        if ($this->request->has('size3_count')) {
            $rules['size3_count'] = 'required|numeric|min:1';
        }

        if ($this->request->has('size4_count')) {
            $rules['size4_count'] = 'required|numeric|min:1';
        }

        if (
            !$this->request->has('free_size_count') &&
            !$this->request->has('size1_count') &&
            !$this->request->has('size2_count') &&
            !$this->request->has('size3_count') &&
            !$this->request->has('size4_count')
        ) {
            $rules['free_size_count'] = 'required|numeric|min:1';
            $rules['size1_count'] = 'required|numeric|min:1';
            $rules['size2_count'] = 'required|numeric|min:1';
            $rules['size3_count'] = 'required|numeric|min:1';
            $rules['size4_count'] = 'required|numeric|min:1';
        }

        return $rules;
    }
}
