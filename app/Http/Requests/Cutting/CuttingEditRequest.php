<?php

namespace App\Http\Requests\Cutting;

use Illuminate\Foundation\Http\FormRequest;

class CuttingEditRequest extends FormRequest
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
            'id' => $this->id,
        ]);
    }

    protected function getValidatorInstance(){
        $validator = parent::getValidatorInstance();

        $validator->sometimes('free_size_count', 'required|numeric|min:1', function($input)
        {
            return
                !isset($input->size1_count) &&
                !isset($input->size2_count) &&
                !isset($input->size3_count) &&
                !isset($input->size4_count);
        });

        $validator->sometimes('size1_count', 'required|numeric|min:1', function($input)
        {
            return !isset($input->free_size_count) &&
                !isset($input->size2_count) &&
                !isset($input->size3_count) &&
                !isset($input->size4_count);
        });

        $validator->sometimes('size2_count', 'required|numeric|min:1', function($input)
        {
            return !isset($input->free_size_count) &&
                !isset($input->size1_count) &&
                !isset($input->size3_count) &&
                !isset($input->size4_count);
        });

        $validator->sometimes('size3_count', 'required|numeric|min:1', function($input)
        {
            return !isset($input->free_size_count) &&
                !isset($input->size1_count) &&
                !isset($input->size2_count) &&
                !isset($input->size4_count);
        });

        $validator->sometimes('size4_count', 'required|numeric|min:1', function($input)
        {
            return !isset($input->free_size_count) &&
                !isset($input->size1_count) &&
                !isset($input->size2_count) &&
                !isset($input->size3_count);
        });

        return $validator;
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
            'id' => 'required|numeric|min:1',
            'cutted_count' => 'required|numeric|min:1',
            'free_size_count' => 'required|numeric|min:1',
            'size1_count' => 'required|numeric|min:1',
            'size2_count' => 'required|numeric|min:1',
            'size3_count' => 'required|numeric|min:1',
            'size4_count' => 'required|numeric|min:1',
        ];
    }
}
