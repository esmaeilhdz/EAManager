<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPerson;
use App\Repositories\Interfaces\iSalary;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class SalaryHelper
{
    use Common;

    // attributes
    public iPerson $person_interface;
    public iSalary $salary_interface;

    public function __construct(
        iPerson $person_interface,
        iSalary $salary_interface
    )
    {
        $this->person_interface = $person_interface;
        $this->salary_interface = $salary_interface;
    }

    /**
     * سرویس لیست حقوق های افراد
     * @param $inputs
     * @return array
     */
    public function getAllSalaries($inputs): array
    {
        $user = Auth::user();
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;family;national_code;concat_ws(" ",name,family);replace(concat_ws("",name,family)," ","")');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'salaries');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $salaries = $this->salary_interface->getAllSalaries($inputs, $user);

        $salaries->transform(function ($item) {
            return [
                'id' => $item->id,
                'person' => [
                    'code' => $item->person->code,
                    'name' => $item->person->name,
                    'family' => $item->person->family,
                ],
                'from_date' => $item->from_date,
                'to_date' => $item->to_date,
                'reward_price' => $item->reward_price,
                'overtime_hour' => $item->overtime_hour,
                'salary_deduction' => $item->salary_deduction,
                'creator' => is_null($item->creator->person) ? null : [
                    'person' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $salaries
        ];
    }

    /**
     * سرویس لیست حقوق های فرد
     * @param $inputs
     * @return array
     */
    public function getSalaries($inputs): array
    {
        $select = ['id', 'code', 'name', 'family'];
        $person = $this->person_interface->getPersonByCode($inputs['code'], $select);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        /*$search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;family;national_code;concat_ws(" ",name,family);replace(concat_ws("",name,family)," ","")');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;*/

        $inputs['person_id'] = $person->id;
        $inputs['order_by'] = $this->orderBy($inputs, 'salaries');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $user = Auth::user();
        $salaries = $this->salary_interface->getSalaries($inputs, $user);

        $salaries->transform(function ($item) {
            return [
                'id' => $item->id,
                'from_date' => $item->from_date,
                'to_date' => $item->to_date,
                'reward_price' => $item->reward_price,
                'overtime_hour' => $item->overtime_hour,
                'salary_deduction' => $item->salary_deduction,
                'creator' => is_null($item->creator->person) ? null : [
                    'person' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $salaries,
            'other' => [
                'person' => [
                    'code' => $person->code,
                    'name' => $person->name,
                    'family' => $person->family
                ]
            ]
        ];
    }

    /**
     * سرویس جزئیات حقوق فرد
     * @param $inputs
     * @return array
     */
    public function getSalaryDetail($inputs): array
    {
        $select = ['id'];
        $person = $this->person_interface->getPersonByCode($inputs['code'], $select);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['person_id'] = $person->id;
        $user = Auth::user();
        $select = ['person_id', 'from_date', 'to_date', 'reward_price', 'overtime_hour', 'salary_deduction', 'description'];
        $relation = [
            'person:id,code,name,family'
        ];
        $salary = $this->salary_interface->getSalaryDetail($inputs, $user, $select, $relation);
        if (is_null($salary)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $salary
        ];
    }

    /**
     * سرویس ویرایش حقوق فرد
     * @param $inputs
     * @return array
     */
    public function editSalary($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['code']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $user = Auth::user();
        $inputs['person_id'] = $person->id;
        $salary = $this->salary_interface->getSalaryDetail($inputs, $user);
        if (is_null($salary)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->salary_interface->editSalary($salary, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن حقوق فرد
     * @param $inputs
     * @return array
     */
    public function addSalary($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['code']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['person_id'] = $person->id;
        $user = Auth::user();
        $result = $this->salary_interface->addSalary($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }


}
