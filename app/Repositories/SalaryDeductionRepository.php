<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\SalaryDeduction;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SalaryDeductionRepository implements Interfaces\iSalaryDeduction
{
    use Common;

    /**
     * لیست کسورات حقوق
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getSalaryDeductions($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return SalaryDeduction::with([
                'product:id,name',
                'creator:id,person_id',
                'creator.person:id,name,family'
            ])
                ->select([
                    'id',
                    'product_id',
                    'price',
                    'created_by'
                ])
                ->where('salary_id', $inputs['salary_id'])
                ->whereHas('salary', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات کسر حقوق
     * @param $inputs
     * @param $user
     * @param array $relation
     * @return object
     * @throws ApiException
     */
    public function getSalaryDeductionDetail($inputs, $user, $relation = []): object
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $salary_deduction = SalaryDeduction::select([
                'id',
                'price',
                'product_id',
                'description'
            ]);

            if (count($relation)) {
                $salary_deduction = $salary_deduction->with($relation);
            }

            return $salary_deduction->where('salary_id', $inputs['salary_id'])
                ->where('id', $inputs['id'])
                ->whereHas('salary', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش کسر حقوق
     * @param $salary_deduction
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editSalaryDeduction($salary_deduction, $inputs)
    {
        try {
            $salary_deduction->price = $inputs['price'];
            $salary_deduction->product_id = $inputs['product_id'];
            $salary_deduction->description = $inputs['description'];

            return $salary_deduction->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن کسر حقوق
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addSalaryDeduction($inputs, $user)
    {
        try {
            $salary_deduction = new SalaryDeduction();

            $salary_deduction->salary_id = $inputs['salary_id'];
            $salary_deduction->price = $inputs['price'];
            $salary_deduction->product_id = $inputs['product_id'] ?? null;
            $salary_deduction->description = $inputs['description'] ?? null;
            $salary_deduction->created_by = $user->id;

            $result = $salary_deduction->save();

            return [
                'result' => $result,
                'data' => $result ? $salary_deduction->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف کسر حقوق
     * @param $salary_deduction
     * @return mixed
     * @throws ApiException
     */
    public function deleteSalaryDeduction($salary_deduction)
    {
        try {
            return $salary_deduction->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
