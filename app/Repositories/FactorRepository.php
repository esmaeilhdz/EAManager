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
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'code',
                    'customer_id',
                    'factor_no',
                    'has_return_permission',
                    'is_credit',
                    'status',
                    'settlement_date',
                    'final_price',
                    'created_by',
                    'created_at'
                ])
                ->where(function ($q) use ($inputs) {
                    $q->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                        ->orWhereHas('customer', function ($q) use ($inputs) {
                            $q->whereRaw($inputs['where']['customer']['condition'], $inputs['where']['customer']['params']);
                        });
                })
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * لیست فاکتورهای قابل بستن
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getCompletableFactors($inputs): LengthAwarePaginator
    {
        try {
            // کالاهای فاکتورهای ناقص
            /*$in_complete_products = Factor::select([
                'factors.code',
                'factors.factor_no',
                'pw.product_id',
                'fp.free_size_count',
                'fp.size1_count',
                'fp.size2_count',
                'fp.size3_count',
                'fp.size4_count'
            ])
                ->join('factor_products as fp', 'factors.id', '=', 'fp.factor_id')
                ->join('product_warehouses as pw', 'pw.id', '=', 'fp.product_warehouse_id')
                ->where('factors.status', 1)
                ->get();

            $where = '';
            foreach ($in_complete_products as $item) {
                $where .= "(
                    product_id = $item->product_id
                    AND free_size_count >= $item->free_size_count
                    AND size1_count >= $item->size1_count
                    AND size2_count >= $item->size2_count
                    AND size3_count >= $item->size3_count
                    AND size4_count >= $item->size4_count
                ) AND ";
            }
            $where = rtrim($where, ' AND ');*/

            return Factor::query()
                ->with([
                    'customer:id,name,mobile',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'code',
                    'customer_id',
                    'factor_no',
                    'has_return_permission',
                    'is_credit',
                    'status',
                    'settlement_date',
                    'final_price',
                    'factors.created_by',
                    'factors.created_at'
                ])
                ->join('factor_products as fp', 'factors.id', '=', 'fp.factor_id')
                ->join('product_warehouses as pw', 'pw.id', '=', 'fp.product_warehouse_id')
                ->where(function ($q) use ($inputs) {
                    $q->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                        ->orWhereHas('customer', function ($q) use ($inputs) {
                            $q->whereRaw($inputs['where']['customer']['condition'], $inputs['where']['customer']['params']);
                        });
                })
//                ->whereRaw($where)
                ->where('status', 1)
                ->orderByRaw($inputs['order_by'])
                ->groupBy('factors.code')
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات فاکتور
     * @param $code
     * @param $user
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getFactorByCode($code, $user, $select = [], $relation = []): mixed
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $factor = Factor::whereCode($code)
                ->where('company_id', $company_id);

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
            $factor->factor_no = $inputs['factor_no'];
            $factor->has_return_permission = $inputs['has_return_permission'];
            $factor->is_credit = $inputs['is_credit'];
            $factor->settlement_date = $inputs['settlement_date'];
            $factor->description = $inputs['description'];
            $factor->final_price = $inputs['final_price'];

            return $factor->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * تغییر وضعیت فاکتور
     * @param $factor
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function changeStatusFactor($factor, $inputs): mixed
    {
        try {
            $factor->status = $inputs['status'];
            // مرجوع فاکتور
            if ($inputs['status'] == 3) {
                $factor->returned_at = now();
            }

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
            $company_id = $this->getCurrentCompanyOfUser($user);
            $factor = new Factor();

            $factor->code = $this->randomString();
            $factor->company_id = $company_id;
            $factor->customer_id = $inputs['customer_id'];
            $factor->factor_no = 'FF-'.rand(1111111,9999999);
            $factor->has_return_permission = $inputs['has_return_permission'];
            $factor->is_credit = $inputs['is_credit'];
            $factor->status = $inputs['status'];
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
