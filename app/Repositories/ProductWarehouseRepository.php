<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductWarehouseRepository implements Interfaces\iProductWarehouse
{
    use Common;

    /**
     * لیست انبارهای کالای شرکت کاربر
     * @param $product_id
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getProductWarehouses($product_id, $inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductWarehouse::query()
                ->with([
                    'place:id,name',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'id',
                    'place_id',
                    'free_size_count',
                    'size1_count',
                    'size2_count',
                    'size3_count',
                    'size4_count',
                    'is_enable',
                    'created_by',
                    'created_at'
                ])
                ->where('product_id', $product_id)
                ->where('company_id', $company_id)
//                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات انبار کالا
     * @param $inputs
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getProductWarehouseById($inputs, $select = [], $relation = []): mixed
    {
        try {
            $product_warehouse = ProductWarehouse::where('id', $inputs['warehouse_id'])
                ->where('product_id', $inputs['product_id']);

            if (count($relation)) {
                $product_warehouse = $product_warehouse->with($relation);
            }

            if (count($select)) {
                $product_warehouse = $product_warehouse->select($select);
            }

            return $product_warehouse->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getByProductId($id, $select = [], $relation = [])
    {
        try {
            $product_warehouse = ProductWarehouse::where('product_id', $id);

            if (count($relation)) {
                $product_warehouse = $product_warehouse->with($relation);
            }

            if (count($select)) {
                $product_warehouse = $product_warehouse->select($select);
            }

            return $product_warehouse->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش انبار کالا
     * @param $product_warehouse
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editProductWarehouse($product_warehouse, $inputs): mixed
    {
        try {
            if (!empty($inputs['place_id'])) {
                $product_warehouse->place_id = $inputs['place_id'];
            }

            if (isset($inputs['sign'])) {
                if ($inputs['sign'] == 'plus') {
                    $product_warehouse->free_size_count += $inputs['free_size_count'];
                    $product_warehouse->size1_count += $inputs['size1_count'];
                    $product_warehouse->size2_count += $inputs['size2_count'];
                    $product_warehouse->size3_count += $inputs['size3_count'];
                    $product_warehouse->size4_count += $inputs['size4_count'];
                } elseif ($inputs['sign'] == 'minus') {
                    $product_warehouse->free_size_count -= $inputs['free_size_count'];
                    $product_warehouse->size1_count -= $inputs['size1_count'];
                    $product_warehouse->size2_count -= $inputs['size2_count'];
                    $product_warehouse->size3_count -= $inputs['size3_count'];
                    $product_warehouse->size4_count -= $inputs['size4_count'];
                }
            } else {
                $product_warehouse->free_size_count = $inputs['free_size_count'];
                $product_warehouse->size1_count = $inputs['size1_count'];
                $product_warehouse->size2_count = $inputs['size2_count'];
                $product_warehouse->size3_count = $inputs['size3_count'];
                $product_warehouse->size4_count = $inputs['size4_count'];
            }

            if (isset($inputs['is_enable'])) {
                $product_warehouse->is_enable = $inputs['is_enable'];
            }

            return $product_warehouse->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * غیرفعال سازی انبار یک کالا
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function deActiveOldWarehouses($inputs): mixed
    {
        try {
            return ProductWarehouse::where('product_id', $inputs['product_id'])
                ->where('place_id', $inputs['place_id'])
                ->update(['is_enable' => 0]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن انبار کالا
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addProductWarehouse($inputs, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);

            $product_warehouse = new ProductWarehouse();

            $product_warehouse->company_id = $company_id;
            $product_warehouse->product_id = $inputs['product_id'];
            $product_warehouse->place_id = $inputs['place_id'];
            $product_warehouse->free_size_count = $inputs['free_size_count'];
            $product_warehouse->size1_count = $inputs['size1_count'];
            $product_warehouse->size2_count = $inputs['size2_count'];
            $product_warehouse->size3_count = $inputs['size3_count'];
            $product_warehouse->size4_count = $inputs['size4_count'];
            $product_warehouse->created_by = $user->id;

            $result = $product_warehouse->save();

            return [
                'result' => $result,
                'data' => $result ? $product_warehouse->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف انبار کالا
     * @param $product
     * @return mixed
     * @throws ApiException
     */
    public function deleteProductWarehouse($product_warehouse): mixed
    {
        try {
            return $product_warehouse->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
