<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Factor;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FactorRepository implements Interfaces\iFactor
{
    use Common;

    /**
     * لیست فاکتورها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getFactors($inputs): LengthAwarePaginator
    {
        try {
            return Factor::query()
                ->with([
                    'customer:id,name,mobile',
                    'sale_period:id,name',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'code',
                    'customer_id',
                    'sale_period_id',
                    'factor_no',
                    'has_return_permission',
                    'is_credit',
                    'is_complete',
                    'settlement_date',
                    'final_price',
                    'created_by',
                    'created_at'
                ])
                ->where(function ($q) use ($inputs) {
                    $q->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                        ->orWhereHas('customer', function ($q) use ($inputs) {
                            $q->whereRaw($inputs['where']['customer']['condition'], $inputs['where']['customer']['params']);
                        })
                        ->orWhereHas('sale_period', function ($q) use ($inputs) {
                            $q->whereRaw($inputs['where']['sale_period']['condition'], $inputs['where']['sale_period']['params']);
                        });
                })
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات فاکتور
     * @param $code
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getFactorByCode($code, $select = [], $relation = []): mixed
    {
        try {
            $factor = Factor::whereCode($code);

            if (count($relation)) {
                $factor = $factor->with($relation);
            }

            if (count($select)) {
                $factor = $factor->select($select);
            }

            return $factor->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش فاکتور
     * @param $factor
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editFactor($factor, $inputs): mixed
    {
        try {
            $factor->customer_id = $inputs['customer_id'];
            $factor->sale_period_id = $inputs['sale_period_id'];
            $factor->factor_no = $inputs['factor_no'];
            $factor->has_return_permission = $inputs['has_return_permission'];
            $factor->is_credit = $inputs['is_credit'];
            $factor->is_complete = $inputs['is_complete'];
            $factor->settlement_date = $inputs['settlement_date'];
            $factor->description = $inputs['description'];
            $factor->final_price = $inputs['final_price'];

            return $factor->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function changeCompleteFactor($factor, $inputs)
    {
        try {
            $factor->is_complete = $inputs['is_complete'];

            return $factor->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن فاکتور
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addFactor($inputs, $user): array
    {
        try {
            $factor = new Factor();

            $factor->code = $this->randomString();
            $factor->customer_id = $inputs['customer_id'];
            $factor->sale_period_id = $inputs['sale_period_id'];
            $factor->factor_no = $inputs['factor_no'];
            $factor->has_return_permission = $inputs['has_return_permission'];
            $factor->is_credit = $inputs['is_credit'];
            $factor->is_complete = $inputs['is_complete'];
            $factor->settlement_date = $inputs['settlement_date'];
            $factor->description = $inputs['description'] ?? null;
            $factor->final_price = $inputs['final_price'];
            $factor->created_by = $user->id;

            $result = $factor->save();

            return [
                'result' => $result,
                'data' => $result ? [
                    'code' => $factor->code,
                    'id' => $factor->id
                ] : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف فاکتور
     * @param $factor
     * @return mixed
     * @throws ApiException
     */
    public function deleteFactor($factor): mixed
    {
        try {
            return $factor->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
