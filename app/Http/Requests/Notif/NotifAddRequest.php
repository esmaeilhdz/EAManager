<?php

namespace App\Http\Requests\Notif;

use Illuminate\Foundation\Http\FormRequest;

class NotifAddRequest extends FormRequest
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
            'subject' => 'required|string',
            'description' => 'required|string',
            'receiver_user_id' => 'required|numeric|min:1',
            'notification_level_id' => 'required|numeric|min:1',
        ];
    }
}
