<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ProductPrice;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductPriceRepository implements Interfaces\iProductPrice
{
    use Common;

    /**
     * لیست کالاها
     * @param $product_id
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getProductPrices($product_id, $inputs): LengthAwarePaginator
    {
        try {
            return ProductPrice::query()
                ->with([
                    'product_accessory_price.product_accessory.model',
                    'cutter_person:id,name,family',
                    'cutter_place:id,name',
                ])
                ->select([
                    'id',
                    'cutter_person_id',
                    'cutter_place_id',
                    'total_count',
                    'serial_count',
                    'sewing_price',
                    'sewing_date',
                    'cutting_price',
                    'cutting_date',
                    'packing_price',
                    'sending_price',
                    'sewing_final_price',
                    'sale_profit_price',
                    'final_price',
                    'is_enable',
                    'created_by',
                    'created_at'
                ])
                ->where('product_id', $product_id)
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات قیمت کالا
     * @param $inputs
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getProductPriceById($inputs, $select = [], $relation = []): mixed
    {
        try {
            $product_price = ProductPrice::where('product_id', $inputs['product_id'])
                ->where('id', $inputs['id']);

            if (count($relation)) {
                $product_price = $product_price->with($relation);
            }

            if (count($select)) {
                $product_price = $product_price->select($select);
            }

            return $product_price->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش قیمت کالا
     * @param $product_price
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editProductPrice($product_price, $inputs): mixed
    {
        try {
            $product_price->product_id = $inputs['product_id'];
            $product_price->cutter_person_id = $inputs['cutter_person_id'];
            $product_price->total_count = $inputs['total_count'];
            $product_price->serial_count = $inputs['serial_count'];
            $product_price->sewing_date = $inputs['sewing_date'];
            $product_price->sewing_price = $inputs['sewing_price'];
            $product_price->cutting_date = $inputs['cutting_date'];
            $product_price->cutting_price = $inputs['cutting_price'];
            $product_price->packing_price = $inputs['packing_price'];
            $product_price->sending_price = $inputs['sending_price'];
            $product_price->sewing_final_price = $inputs['sewing_final_price'];
            $product_price->sale_profit_price = $inputs['sale_profit_price'];
            $product_price->final_price = $inputs['final_price'];

            return $product_price->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * غیرفعال سازی قیمت های قبلی محصول
     * @param $product_id
     * @return mixed
     * @throws ApiException
     */
    public function deActiveOldPrices($product_id): mixed
    {
        try {
            return ProductPrice::where('product_id', $product_id)->update([
                'is_enable' => 0
            ]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن قیمت کالا
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addProductPrice($inputs, $user): array
    {
        try {
            $product_price = new ProductPrice();

            $product_price->product_id = $inputs['product_id'];
            $product_price->cutter_person_id = $inputs['cutter_person_id'] ?? null;
            $product_price->cutter_place_id = $inputs['cutter_place_id'] ?? null;
            $product_price->total_count = $inputs['total_count'];
            $product_price->serial_count = $inputs['serial_count'];
            $product_price->sewing_date = $inputs['sewing_date'];
            $product_price->sewing_price = $inputs['sewing_price'];
            $product_price->cutting_date = $inputs['cutting_date'];
            $product_price->cutting_price = $inputs['cutting_price'];
            $product_price->packing_price = $inputs['packing_price'];
            $product_price->sending_price = $inputs['sending_price'];
            $product_price->sewing_final_price = $inputs['sewing_final_price'];
            $product_price->sale_profit_price = $inputs['sale_profit_price'];
            $product_price->final_price = $inputs['final_price'];
            $product_price->created_by = $user->id;

            $result = $product_price->save();

            return [
                'result' => $result,
                'data' => $result ? $product_price->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف قیمت کالا
     * @param $product_price
     * @return mixed
     * @throws ApiException
     */
    public function deleteProductPrice($product_price): mixed
    {
        try {
            return $product_price->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
