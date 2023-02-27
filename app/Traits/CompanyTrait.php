<?php

namespace App\Traits;

use App\Exceptions\ApiException;
use App\Models\Company;

trait CompanyTrait
{
    use Common;

    /**
     * id های شرکت های زیرمجموعه شرکت کاربر
     * @param $user
     * @return mixed
     * @throws ApiException
     */
    public function getChildIdsOfMyCompany($user): mixed
    {
        try {
            $company_ids = [];
            $company_id = $this->getCurrentCompanyOfUser($user);
            $company_array = Company::query()->select(['id', 'parent_id'])->get()->all();
            $company_ids_temp = array_column($company_array, 'id');
            $parent_id = array_search($company_id, $company_ids_temp);
            $company_child_tree = $this->buildTree($company_array, $parent_id);

            return $this->convertTreeToArray($company_child_tree, $company_ids);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
