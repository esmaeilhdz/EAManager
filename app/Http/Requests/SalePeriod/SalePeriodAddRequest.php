<?php

namespace App\Http\Requests\SalePeriod;

use Illuminate\Foundation\Http\FormRequest;

class SalePeriodAddRequest extends FormRequest
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
        list($year, $month, $day) = explode('-', date('Y-m-d'));
        $start_date = "$year-01-01";
        $end_date = "$year-12-30";
        return [
            'name' => 'required|string',
            'start_date' => "required|date|after:$start_date",
            'end_date' => "nullable|date|before:$end_date"
        ];
    }
}
