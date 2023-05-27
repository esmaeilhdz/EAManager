<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Repositories\Interfaces\iPerson;
use App\Repositories\Interfaces\iPersonCompany;
use App\Repositories\Interfaces\iSalary;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class SalaryHelper
{
    use Common;

    // attributes
    public iPerson $person_interface;
    public iSalary $salary_interface;
    public iPersonCompany $person_company_interface;

    public function __construct(
        iPerson $person_interface,
        iSalary $salary_interface,
        iPersonCompany $person_company_interface,
    )
    {
        $this->person_interface = $person_interface;
        $this->salary_interface = $salary_interface;
        $this->person_company_interface = $person_company_interface;
    }

    /**
     * @throws ApiException
     */
    private function calculatePayable($person_id, $user, $reward_price, $overtime_hour, $deduction_price): float
    {
        $company_id = $this->getCurrentCompanyOfUser($user);
        $person_company = $this->person_company_interface->getPersonCompanyDetail($person_id, $company_id);

        $salary_per_hour = $person_company->daily_income / 8;
        return ($person_company->suggest_salary + $reward_price + ($salary_per_hour * 1.4 * $overtime_hour)) - $deduction_price;
    }


    /**
     * سرویس لیست حقوق های افراد
     * @param $inputs
     * @return array
     * @throws ApiException
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

        $salaries->transform(function ($item) use ($user) {
            $sum_deduction = 0;
            foreach ($item->salary_deduction as $salary_deduction) {
                $sum_deduction += $salary_deduction->price;
            }

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
                'sum_deduction' => $sum_deduction,
                'is_checkout' => $item->is_checkout,
                'payable' => $this->calculatePayable($item->person_id, $user, $item->reward_price, $item->overtime_hour, $sum_deduction),
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
     * @throws ApiException
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

        $inputs['person_id'] = $person->id;
        $inputs['order_by'] = $this->orderBy($inputs, 'salaries');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $user = Auth::user();
        $salaries = $this->salary_interface->getSalaries($inputs, $user);

        $salaries->transform(function ($item) use ($person, $user) {
            $sum_deduction = 0;
            foreach ($item->salary_deduction as $salary_deduction) {
                $sum_deduction += $salary_deduction->price;
            }

            return [
                'id' => $item->id,
                'from_date' => $item->from_date,
                'to_date' => $item->to_date,
                'reward_price' => $item->reward_price,
                'overtime_hour' => $item->overtime_hour,
                'sum_deduction' => $sum_deduction,
                'is_checkout' => $item->is_checkout,
                'payable' => $this->calculatePayable($person->id, $user, $item->reward_price, $item->overtime_hour, $sum_deduction),
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
        $select = ['person_id', 'from_date', 'to_date', 'reward_price', 'overtime_hour', 'is_checkout', 'description'];
        $relation = [
            'salary_deduction:salary_id,product_id,price,description',
            'salary_deduction.product:id,name',
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

        $salary_deductions = null;
        foreach ($salary->salary_deduction as $salary_deduction) {
            $salary_deductions[] = [
                'product_name' => $salary_deduction->product->name,
                'price' => $salary_deduction->price,
                'description' => $salary_deduction->description,
            ];
        }

        $return = [
            'person' => [
                'name' => $salary->person->name,
                'family' => $salary->person->family
            ],
            'from_date' => $salary->from_date,
            'to_date' => $salary->to_date,
            'reward_price' => $salary->reward_price,
            'overtime_hour' => $salary->overtime_hour,
            'is_checkout' => $salary->is_checkout,
            'description' => $salary->description,
            'salary_deductions' => $salary_deductions
        ];

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $return
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
