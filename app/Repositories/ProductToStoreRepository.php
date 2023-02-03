<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Models\ProductToStore;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductToStoreRepository implements Interfaces\iProductToStore
{
    use Common;

    /**
     * لیست ارسال کالا به فروشگاه
     * @param $product_warehouse
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getProductToStores($product_warehouse, $inputs): LengthAwarePaginator
    {
        try {
            return ProductToStore::query()
                ->with([
                    'productWarehouse:id,place_id',
                    'productWarehouse.place:id,name',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'id',
                    'product_warehouse_id',
                    'free_size_count',
                    'size1_count',
                    'size2_count',
                    'size3_count',
                    'size4_count',
                    'created_by',
                    'created_at'
                ])
                ->where('product_warehouse_id', $product_warehouse->id)
                ->whereHas('productWarehouse.place', function ($q) use ($inputs) {
                    $q->whereRaw($inputs['where']['place']['condition'], $inputs['where']['place']['params']);
                })
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات ارسال کالا به فروشگاه
     * @param $product_warehouse_id
     * @param $id
     * @param array $select
     * @return mixed
     * @throws ApiException
     */
    public function getProductToStoreById($product_warehouse_id, $id, $select = []): mixed
    {
        try {
            $product = ProductToStore::with([
                'productWarehouse:id,place_id',
                'productWarehouse.place:id,name'
            ]);

            if (count($select)) {
                $product = $product->select($select);
            }

            return $product->where('product_warehouse_id', $product_warehouse_id)
                ->where('id', $id)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش ارسال کالا به فروشگاه
     * @param $product_to_store
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editProductToStore($product_to_store, $inputs): mixed
    {
        try {
            $product_to_store->free_size_count = $inputs['free_size_count'];
            $product_to_store->size1_count = $inputs['size1_count'];
            $product_to_store->size2_count = $inputs['size2_count'];
            $product_to_store->size3_count = $inputs['size3_count'];
            $product_to_store->size4_count = $inputs['size4_count'];

            return $product_to_store->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن ارسال کالا به فروشگاه
     * @param $inputs
     * @param $user
     * @param bool $quiet
     * @return array
     * @throws ApiException
     */
    public function addProductToStore($inputs, $user, $quiet = false): array
    {
        try {
            $product_to_store = new ProductToStore();

            $product_to_store->product_warehouse_id = $inputs['product_warehouse_id'];
            $product_to_store->free_size_count = $inputs['free_size_count'];
            $product_to_store->size1_count = $inputs['size1_count'];
            $product_to_store->size2_count = $inputs['size2_count'];
            $product_to_store->size3_count = $inputs['size3_count'];
            $product_to_store->size4_count = $inputs['size4_count'];
            $product_to_store->description = $inputs['description'];
            $product_to_store->created_by = $user->id;

            if (!$quiet) {
                $result = $product_to_store->save();
            } else {
                $result = $product_to_store->saveQuietly();
            }

            return [
                'result' => $result,
                'data' => $result ? $product_to_store->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف ارسال کالا به فروشگاه
     * @param $product_to_store
     * @return mixed
     * @throws ApiException
     */
    public function deleteProductToStore($product_to_store): mixed
    {
        try {
            return $product_to_store->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
