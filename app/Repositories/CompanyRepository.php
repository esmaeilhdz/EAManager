<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Company;
use App\Traits\Common;
use App\Traits\CompanyTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompanyRepository implements Interfaces\iCompany
{
    use Common, CompanyTrait;

    /**
     * لیست شرکت ها
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getCompanies($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_ids = $this->getChildIdsOfMyCompany($user);

            return Company::query()
                ->with([
                    'creator:id,person_id',
                    'creator.person:id,name,family',
                    'parent:id,code,name'
                ])
                ->select([
                    'code',
                    'name',
                    'is_enable',
                    'created_by',
                    'created_at'
                ])
                ->whereIn('id', $company_ids)
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getCompanyCombo($inputs, $user)
    {
        try {
            $company_ids = $this->getChildIdsOfMyCompany($user);

            return Company::query()
                ->select([
                    'code',
                    'name'
                ])
                ->whereIn('id', $company_ids)
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->orderByRaw($inputs['order_by'])
                ->limit(20)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات شرکت
     * @param $code
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getCompanyByCode($code, $select = [], $relation = []): mixed
    {
        try {
            $company = Company::whereCode($code);

            if (count($relation)) {
                $company = $company->with($relation);
            }

            if (count($select)) {
                $company = $company->select($select);
            }

            return $company->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش شرکت
     * @param $company
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editCompany($company, $inputs): mixed
    {
        try {
            $company->parent_id = $inputs['parent_id'];
            $company->name = $inputs['name'];
            $company->is_enable = $inputs['is_enable'];

            return $company->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن شرکت
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addCompany($inputs, $user): array
    {
        try {
            $company = new Company();

            $company->code = $this->randomString();
            $company->parent_id = $inputs['parent_id'];
            $company->name = $inputs['name'];
            $company->created_by = $user->id;

            $result = $company->save();

            return [
                'result' => $result,
                'data' => $result ? $company->code : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف شرکت
     * @param $company
     * @return mixed
     * @throws ApiException
     */
    public function deleteCompany($company): mixed
    {
        try {
            return $company->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
