<?php

namespace App\Http\Requests\ChatGroupPerson;

use Illuminate\Foundation\Http\FormRequest;

class ChatGroupPersonDetailRequest extends FormRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'chat_group_id' => $this->chat_group_id,
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
            'chat_group_id' => 'required|numeric|min:1',
            'id' => 'required|numeric|min:1',
        ];
    }
}
