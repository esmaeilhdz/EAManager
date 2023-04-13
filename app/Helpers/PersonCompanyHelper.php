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

        $person_company = $this->person_company_interface->getPersonCompanyDetail($person->id, $company->id);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $person_company
        ];
    }

    /**
     * سرویس ویرایش ارتباط شرکت و فرد
     * @param $inputs
     * @return array
     */
    public function editPersonCompany($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['person_code'], ['id']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
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

        $person_company = $this->person_company_interface->getPersonCompanyDetail($person->id, $company->id);

        $result = $this->person_company_interface->editPersonCompany($inputs, $person_company);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس ویرایش وضعیت ارتباط شرکت و فرد
     * @param $inputs
     * @return array
     */
    public function changePersonCompany($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['person_code'], ['id']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
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

        $person_company = $this->person_company_interface->getPersonCompanyDetail($person->id, $company->id);

        $result = $this->person_company_interface->changePersonCompany($inputs, $person_company);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن ارتباط شرکت و فرد
     * @param $inputs
     * @return array
     */
    public function addPersonCompany($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['person_code'], ['id']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
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
        $inputs['person_id'] = $person->id;
        $inputs['company_id'] = $company->id;

        $user = Auth::user();
        $result = $this->person_company_interface->addPersonCompany($inputs, $user);

        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * سرویس حذف ارتباط شرکت و فرد
     * @param $inputs
     * @return array
     */
    public function deletePersonCompany($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['person_code'], ['id']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
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

        $person_company = $this->person_company_interface->getPersonCompanyDetail($person->id, $company->id);
        if (is_null($person_company)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->person_company_interface->deletePersonCompany($person_company);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
