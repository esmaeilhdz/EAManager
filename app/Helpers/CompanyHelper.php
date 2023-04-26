<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCompany;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class CompanyHelper
{
    use Common;

    // attributes
    public iCompany $company_interface;

    public function __construct(iCompany $company_interface)
    {
        $this->company_interface = $company_interface;
    }

    /**
     * سرویس لیست شرکت ها
     * @param $inputs
     * @return array
     */
    public function getCompanies($inputs): array
    {
        $user = Auth::user();
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'companies');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $companies = $this->company_interface->getCompanies($inputs, $user);

        $companies->transform(function ($item) {
            return [
                'code' => $item->code,
                'name' => $item->name,
                'parent' => is_null($item->parent_id) ? null : $item->parent->name,
                'is_enable' => $item->is_enable,
                'creator' => is_null($item->creator->person) ? null : [
                    'company' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $companies
        ];
    }

    public function getCompanyCombo($inputs)
    {
        $user = Auth::user();
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'companies');
        $companies = $this->company_interface->getCompanyCombo($inputs, $user);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $companies
        ];
    }

    /**
     * سرویس جزئیات شرکت
     * @param $code
     * @return array
     */
    public function getCompanyDetail($code): array
    {
        $select = ['code', 'parent_id', 'name', 'is_enable'];
        $relation = [
            'parent:id,code,name'
        ];
        $company = $this->company_interface->getCompanyByCode($code, $select, $relation);
        if (is_null($company)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $company
        ];
    }

    /**
     * سرویس ویرایش شرکت
     * @param $inputs
     * @return array
     */
    public function editCompany($inputs): array
    {
        $company = $this->company_interface->getCompanyByCode($inputs['code']);
        if (is_null($company)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['parent_id'] = null;
        if (!is_null($inputs['parent_code'])) {
            $parent_company = $this->company_interface->getCompanyByCode($inputs['parent_code'], ['id']);
            if (is_null($parent_company)) {
                return [
                    'result' => false,
                    'message' => __('messages.record_not_found'),
                    'data' => null
                ];
            }
            $inputs['parent_id'] = $parent_company->id;
        }

        $result = $this->company_interface->editCompany($company, $inputs);
        return [
            'result' => (bool)$result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس ویرایش وضعیت شرکت
     * @param $inputs
     * @return array
     */
    public function editCompanyStatus($inputs): array
    {
        $company = $this->company_interface->getCompanyByCode($inputs['code'], ['id', 'is_enable']);
        if (is_null($company)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->company_interface->editCompanyStatus($company, $inputs);
        return [
            'result' => (bool)$result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن شرکت
     * @param $inputs
     * @return array
     */
    public function addCompany($inputs): array
    {
        $inputs['parent_id'] = null;
        if (!is_null($inputs['parent_code'])) {
            $parent_company = $this->company_interface->getCompanyByCode($inputs['parent_code'], ['id']);
            if (is_null($parent_company)) {
                return [
                    'result' => false,
                    'message' => __('messages.record_not_found'),
                    'data' => null
                ];
            }
            $inputs['parent_id'] = $parent_company->id;
        }

        $user = Auth::user();
        $result = $this->company_interface->addCompany($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * سرویس حذف شرکت
     * @param $inputs
     * @return array
     */
    public function deleteCompany($inputs): array
    {
        $company = $this->company_interface->getCompanyByCode($inputs['code'], ['id']);
        if (is_null($company)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->company_interface->deleteCompany($company);
        return [
            'result' => (bool)$result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
