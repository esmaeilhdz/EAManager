<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Factor;
use App\Models\FactorProduct;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FactorProductRepository implements Interfaces\iFactorProduct
{
    use Common;

    public function getById($factor_id, $id, $select = [], $relation = [])
    {
        try {
            $factor_product = FactorProduct::where('factor_id', $factor_id)
                ->where('id', $id);

            if ($select) {
                $factor_product = $factor_product->select($select);
            }

            if ($relation) {
                $factor_product = $factor_product->with($relation);
            }

            return $factor_product->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getByFactorId($factor_id, $inputs, $select = [], $relation = [])
    {
        try {
            $factor_product = FactorProduct::where('factor_id', $factor_id);

            if ($select) {
                $factor_product = $factor_product->select($select);
            }

            if ($relation) {
                $factor_product = $factor_product->with($relation);
            }

            return $factor_product->orderByDesc('id')
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش محصول فاکتور
     * @param $factor_product
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editFactorProduct($factor_product, $inputs): mixed
    {
        try {
            $factor_product->product_warehouse_id = $inputs['product_warehouse_id'];
            $factor_product->free_size_count = $inputs['free_size_count'];
            $factor_product->size1_count = $inputs['size1_count'];
            $factor_product->size2_count = $inputs['size2_count'];
            $factor_product->size3_count = $inputs['size3_count'];
            $factor_product->size4_count = $inputs['size4_count'];
            $factor_product->price = $inputs['price'];

            return $factor_product->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن محصول فاکتور
     * @param array $inputs
     * @param $factor_id
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addFactorProduct(array $inputs, $factor_id, $user): array
    {
        try {
            $factor_product = new FactorProduct();

            $factor_product->factor_id = $factor_id;
            $factor_product->product_warehouse_id = $inputs['product_warehouse_id'];
            $factor_product->free_size_count = $inputs['free_size_count'];
            $factor_product->size1_count = $inputs['size1_count'];
            $factor_product->size2_count = $inputs['size2_count'];
            $factor_product->size3_count = $inputs['size3_count'];
            $factor_product->size4_count = $inputs['size4_count'];
            $factor_product->price = $inputs['price'];

            $result = $factor_product->save();

            return [
                'result' => $result,
                'data' => $result ? $factor_product->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف محصولات فاکتور
     * @param $factor_id
     * @return mixed
     * @throws ApiException
     */
    public function deleteFactorProducts($factor_id): mixed
    {
        try {
            return FactorProduct::where('factor_id', $factor_id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف محصول فاکتور
     * @param $factor_id
     * @param $id
     * @return mixed
     * @throws ApiException
     */
    public function deleteFactorProduct($factor_id, $id): mixed
    {
        try {
            return FactorProduct::where('factor_id', $factor_id)->where('id', $id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
