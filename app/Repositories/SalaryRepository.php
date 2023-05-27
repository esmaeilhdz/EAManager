<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Person;
use App\Models\Salary;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SalaryRepository implements Interfaces\iSalary
{
    use Common;

    /**
     * لیست حقوق های افراد
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getAllSalaries($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Salary::with([
                    'person:id,code,name,family',
                    'salary_deduction:salary_id,price',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'id',
                    'person_id',
                    'from_date',
                    'to_date',
                    'reward_price',
                    'overtime_hour',
                    'is_checkout',
                    'created_by',
                    'created_at'
                ])
                ->whereHas('person', function ($q) use ($inputs) {
                    $q->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params']);
                })
                ->where('company_id', $company_id)
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * لیست حقوق های فرد
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getSalaries($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Salary::with([
                    'salary_deduction:salary_id,price',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'id',
                    'from_date',
                    'to_date',
                    'reward_price',
                    'overtime_hour',
                    'is_checkout',
                    'created_by',
                    'created_at'
                ])
                ->where('person_id', $inputs['person_id'])
                ->where('company_id', $company_id)
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات حقوق فرد
     * @param $inputs
     * @param $user
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getSalaryDetail($inputs, $user, $select = [], $relation = []): mixed
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $salary = Salary::where('person_id', $inputs['person_id'])
                ->where('company_id', $company_id)
                ->where('id', $inputs['id']);

            if (count($relation)) {
                $salary = $salary->with($relation);
            }

            if (count($select)) {
                $salary = $salary->select($select);
            }

            return $salary->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getSalaryById($id, $user, $select = [], $relation = [])
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $salary = Salary::where('company_id', $company_id)
                ->where('id', $id);

            if (count($relation)) {
                $salary = $salary->with($relation);
            }

            if (count($select)) {
                $salary = $salary->select($select);
            }

            return $salary->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش حقوق فرد
     * @param $salary
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editSalary($salary, $inputs): mixed
    {
        try {
            $salary->from_date = $inputs['from_date'];
            $salary->to_date = $inputs['to_date'];
            $salary->reward_price = $inputs['reward_price'];
            $salary->overtime_hour = $inputs['overtime_hour'];
            $salary->is_checkout = $inputs['is_checkout'];
            $salary->description = $inputs['description'];

            return $salary->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن حقوق فرد
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addSalary($inputs, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $salary = new Salary();

            $salary->company_id = $company_id;
            $salary->person_id = $inputs['person_id'];
            $salary->from_date = $inputs['from_date'];
            $salary->to_date = $inputs['to_date'];
            $salary->reward_price = $inputs['reward_price'];
            $salary->overtime_hour = $inputs['overtime_hour'];
            $salary->salary_deduction = $inputs['salary_deduction'];
            $salary->description = $inputs['description'];
            $salary->created_by = $user->id;

            $result = $salary->save();

            return [
                'result' => $result,
                'data' => $result ? $salary->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
