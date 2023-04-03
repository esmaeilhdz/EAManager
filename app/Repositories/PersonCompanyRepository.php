<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\PersonCompany;
use App\Traits\Common;

class PersonCompanyRepository implements Interfaces\iPersonCompany
{
    use Common;

    public function addPersonCompany($inputs, $user)
    {
        try {
            $person_company = new PersonCompany();

            $person_company->person_id = $inputs['person_id'];
            $person_company->company_id = $inputs['company_id'];
            $person_company->start_work_date = $inputs['start_work_date'];
            $person_company->end_work_date = $inputs['end_work_date'];
            $person_company->suggest_salary = $inputs['suggest_salary'];
            $person_company->daily_income = $inputs['daily_income'];
            $person_company->position = $inputs['position'];
            $person_company->created_by = $user->id;

            $result = $person_company->save();

            return [
                'result' => $result,
                'data' => $result ? $person_company->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
