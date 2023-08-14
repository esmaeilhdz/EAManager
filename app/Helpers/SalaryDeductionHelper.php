<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPerson;
use App\Repositories\Interfaces\iSalary;
use App\Repositories\Interfaces\iSalaryDeduction;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class SalaryDeductionHelper
{
    use Common;

    // attributes
    public iSalary $salary_interface;
    public iSalaryDeduction $salary_deduction_interface;

    public function __construct(
        iSalary $salary_interface,
        iSalaryDeduction $salary_deduction_interface
    )
    {
        $this->salary_interface = $salary_interface;
        $this->salary_deduction_interface = $salary_deduction_interface;
    }

    /**
     * سرویس لیست کسورات حقوق
     * @param $inputs
     * @return array
     */
    public function getSalaryDeductions($inputs): array
    {
        $user = Auth::user();
        $select = ['id', 'person_id', 'from_date', 'to_date'];
        $relation = [
            'person:id,name,family'
        ];
        $salary = $this->salary_interface->getSalaryById($inputs['salary_id'], $user, $select, $relation);
        if (is_null($salary)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['order_by'] = $this->orderBy($inputs, 'salary_deductions');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $salary_deductions = $this->salary_deduction_interface->getSalaryDeductions($inputs, $user);

        $salary_deductions->transform(function ($item) {
            return [
                'id' => $item->id,
                'product' => $item->produc->name ?? null,
                'price' => $item->price,
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
            'data' => $salary_deductions,
            'other' => [
                'person' => [
                    'name' => $salary->person->name,
                    'family' => $salary->person->family
                ],
                'from_date' => $salary->from_date,
                'to_date' => $salary->to_date
            ]
        ];
    }

    /**
     * سرویس جزئیات کسر حقوق
     * @param $inputs
     * @return array
     */
    public function getSalaryDeductionDetail($inputs): array
    {
        $user = Auth::user();
        $select = ['id', 'from_date', 'to_date'];
        $salary = $this->salary_interface->getSalaryById($inputs['salary_id'], $user, $select);
        if (is_null($salary)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $relation = [
            'product:id,name'
        ];
        $salary_deduction = $this->salary_deduction_interface->getSalaryDeductionDetail($inputs, $user, $relation);
        if (is_null($salary_deduction)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $salary_deduction
        ];

    }

    /**
     * سرویس ویرایش کسر حقوق
     * @param $inputs
     * @return array
     */
    public function editSalaryDeduction($inputs): array
    {
        $user = Auth::user();
        $select = ['id', 'is_checkout'];
        $salary = $this->salary_interface->getSalaryById($inputs['salary_id'], $user, $select);
        if (is_null($salary)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if ($salary->is_checkout) {
            return [
                'result' => false,
                'message' => __('messages.salary_already_settled_edit_not_allowed'),
                'data' => null
            ];
        }

        $salary_deduction = $this->salary_deduction_interface->getSalaryDeductionDetail($inputs, $user);
        if (is_null($salary_deduction)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->salary_deduction_interface->editSalaryDeduction($salary_deduction, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن کسر حقوق
     * @param $inputs
     * @return array
     */
    public function addSalaryDeduction($inputs): array
    {
        $user = Auth::user();
        $select = ['id', 'is_checkout'];
        $salary = $this->salary_interface->getSalaryById($inputs['salary_id'], $user, $select);
        if (is_null($salary)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if ($salary->is_checkout) {
            return [
                'result' => false,
                'message' => __('messages.salary_already_settled_add_not_allowed'),
                'data' => null
            ];
        }

        $result = $this->salary_deduction_interface->addSalaryDeduction($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * سرویس حذف کسر حقوق
     * @param $inputs
     * @return array
     */
    public function deleteSalaryDeduction($inputs): array
    {
        $user = Auth::user();
        $select = ['id', 'is_checkout'];
        $salary = $this->salary_interface->getSalaryById($inputs['salary_id'], $user, $select);
        if (is_null($salary)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if ($salary->is_checkout) {
            return [
                'result' => false,
                'message' => __('messages.salary_already_settled_delete_not_allowed'),
                'data' => null
            ];
        }

        $salary_deduction = $this->salary_deduction_interface->getSalaryDeductionDetail($inputs, $user);
        if (is_null($salary_deduction)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->salary_deduction_interface->deleteSalaryDeduction($salary_deduction);
        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
