<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Models\RequestProductWarehouse;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RequestProductWarehouseRepository implements Interfaces\iRequestProductWarehouse
{
    use Common;

    /**
     * لیست درخواست های کالا از انبار
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getRequestProductWarehouses($inputs): LengthAwarePaginator
    {
        try {
            return RequestProductWarehouse::query()
                ->with([
                    'request_user:id,person_id',
                    'request_user.person:id,name,family',
                    'confirm_user:id,person_id',
                    'confirm_user.person:id,name,family',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'id',
                    'request_user_id',
                    'confirm_user_id',
                    'free_size_count',
                    'size1_count',
                    'size2_count',
                    'size3_count',
                    'size4_count',
                    'is_confirm',
                    'created_by',
                    'created_at'
                ])
                ->where(function ($q) use ($inputs) {
                    $q->whereHas('request_user.person', function ($q2) use ($inputs) {
                        $q2->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params']);
                    })
                    ->orWhereHas('confirm_user.person', function ($q2) use ($inputs) {
                        $q2->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params']);
                    });
                })
                ->where('product_warehouse_id', $inputs['warehouse_id'])
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات درخواست کالا از انبار
     * @param $inputs
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getRequestProductWarehouseById($inputs, $select = [], $relation = []): mixed
    {
        try {
            $request_product_warehouse = RequestProductWarehouse::where('id', $inputs['id'])
                ->where('product_warehouse_id', $inputs['product_warehouse_id']);

            if (count($relation)) {
                $request_product_warehouse = $request_product_warehouse->with($relation);
            }

            if (count($select)) {
                $request_product_warehouse = $request_product_warehouse->select($select);
            }

            return $request_product_warehouse->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش درخواست کالا از انبار
     * @param $request_product_warehouse
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editRequestProductWarehouse($request_product_warehouse, $inputs): mixed
    {
        try {
            $request_product_warehouse->free_size_count = $inputs['free_size_count'];
            $request_product_warehouse->size1_count = $inputs['size1_count'];
            $request_product_warehouse->size2_count = $inputs['size2_count'];
            $request_product_warehouse->size3_count = $inputs['size3_count'];
            $request_product_warehouse->size4_count = $inputs['size4_count'];

            return $request_product_warehouse->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * تایید درخواست کالا از انبار
     * @param $request_product_warehouse
     * @param $user
     * @return mixed
     * @throws ApiException
     */
    public function confirmRequestProductWarehouse($request_product_warehouse, $user): mixed
    {
        try {
            $request_product_warehouse->is_confirm = 1;
            $request_product_warehouse->confirm_user_id = $user->id;

            return $request_product_warehouse->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن درخواست کالا از انبار
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addRequestProductWarehouse($inputs, $user): array
    {
        try {
            $request_product_warehouse = new RequestProductWarehouse();

            $request_product_warehouse->product_warehouse_id = $inputs['warehouse_id'];
            $request_product_warehouse->request_user_id = $user->id;
            $request_product_warehouse->free_size_count = $inputs['free_size_count'];
            $request_product_warehouse->size1_count = $inputs['size1_count'];
            $request_product_warehouse->size2_count = $inputs['size2_count'];
            $request_product_warehouse->size3_count = $inputs['size3_count'];
            $request_product_warehouse->size4_count = $inputs['size4_count'];
            $request_product_warehouse->created_by = $user->id;

            $result = $request_product_warehouse->save();

            return [
                'result' => $result,
                'data' => $result ? $request_product_warehouse->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف درخواست کالا از انبار
     * @param $request_product_warehouse
     * @return mixed
     * @throws ApiException
     */
    public function deleteRequestProductWarehouse($request_product_warehouse): mixed
    {
        try {
            return $request_product_warehouse->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
