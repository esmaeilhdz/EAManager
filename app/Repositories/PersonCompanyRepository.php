<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\PersonCompany;
use App\Traits\Common;

class PersonCompanyRepository implements Interfaces\iPersonCompany
{
    use Common;

    public function getCompaniesOfPerson($inputs, $user): \Illuminate\Database\Eloquent\Collection|array
    {
        try {
            return PersonCompany::with([
                'company:id,name,code',
                'creator:id,person_id',
                'creator.person:id,name,family'
            ])
                ->select([
                    'person_id',
                    'company_id',
                    'start_work_date',
                    'end_work_date',
                    'suggest_salary',
                    'daily_income',
                    'position',
                    'is_enable',
                    'created_by'
                ])
                ->where('person_id', $inputs['person_id'])
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getPersonCompanyDetail($person_id, $company_id)
    {
        try {
            return PersonCompany::with([

            ])
                ->select([
                    'id',
                    'start_work_date',
                    'end_work_date',
                    'suggest_salary',
                    'daily_income',
                    'position',
                    'is_enable'
                ])
                ->where('person_id', $person_id)
                ->where('company_id', $company_id)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editPersonCompany($inputs, $person_company)
    {
        try {
            $person_company->start_work_date = $inputs['start_work_date'];
            $person_company->end_work_date = $inputs['end_work_date'];
            $person_company->suggest_salary = $inputs['suggest_salary'];
            $person_company->daily_income = $inputs['daily_income'];
            $person_company->position = $inputs['position'];
            $person_company->is_enable = $inputs['is_enable'];

            return $person_company->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function changePersonCompany($inputs, $person_company)
    {
        try {
            $person_company->is_enable = $inputs['is_enable'];

            return $person_company->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

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

    public function deletePersonCompany($person_company)
    {
        try {
            return $person_company->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
