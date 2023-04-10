<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCompany;
use App\Repositories\Interfaces\iPerson;
use App\Repositories\Interfaces\iPersonCompany;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PersonCompanyHelper
{
    use Common;

    // attributes
    public iPerson $person_interface;
    public iCompany $company_interface;
    public iPersonCompany $person_company_interface;

    public function __construct(
        iPerson $person_interface,
        iCompany $company_interface,
        iPersonCompany $person_company_interface,
    )
    {
        $this->person_interface = $person_interface;
        $this->company_interface = $company_interface;
        $this->person_company_interface = $person_company_interface;
    }

    /**
     * سرویس لیست شرکت های یک فرد
     * @param $inputs
     * @return array
     */
    public function getCompaniesOfPerson($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['person_code'], ['id']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.person_not_found'),
                'data' => null
            ];
        }

        $user = Auth::user();

        $inputs['person_id'] = $person->id;
        $inputs['order_by'] = $this->orderBy($inputs, 'person_companies');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $person_companies = $this->person_company_interface->getCompaniesOfPerson($inputs, $user);

        $person_companies->transform(function ($item) {
            return [
                'company' => [
                    'code' => $item->company->code,
                    'name' => $item->company->name
                ],
                'start_work_date' => $item->start_work_date,
                'end_work_date' => is_null($item->end_work_date['gregorian']) ? null : $item->end_work_date,
                'suggest_salary' => $item->suggest_salary,
                'daily_income' => $item->daily_income,
                'position' => $item->position,
                'is_enable' => $item->is_enable,
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
            'data' => $person_companies
        ];
    }

    /**
     * سرویس جزئیات ارتباط شرکت و فرد
     * @param $inputs
     * @return array
     */
    public function getCompanyOfPerson($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['person_code'], ['id']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.person_not_found'),
                'data' => null
            ];
        }

        $company = $this->company_interface->getCompanyByCode($inputs['company_code'], ['id']);
        if (is_null($company)) {
            return [
                'result' => false,
                'message' => __('messages.company_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $person
        ];
    }

    /**
     * سرویس ویرایش فرد
     * @param $inputs
     * @return array
     */
    public function editPerson($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['code']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->person_interface->editPerson($inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن فرد
     * @param $inputs
     * @return array
     */
    public function addPerson($inputs): array
    {
        $company = $this->company_interface->getCompanyByCode($inputs['company_code'], ['id']);
        if (is_null($company)) {
            return [
                'result' => false,
                'message' => __('messages.company_not_found'),
                'data' => null
            ];
        }
        $inputs['company_id'] = $company->id;

        DB::beginTransaction();
        $user = Auth::user();
        $add_person_result = $this->person_interface->addPerson($inputs, $user);
        $result[] = $add_person_result['result'];
        $inputs['person_id'] = $add_person_result['data']->id;

        $result[] = $this->person_company_interface->addPersonCompany($inputs, $user)['result'];

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => $add_person_result['data']->code
        ];
    }

    /**
     * سرویس حذف فرد
     * @param $inputs
     * @return array
     */
    public function deletePerson($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['code'], ['id']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->person_interface->deletePerson($person->id);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
