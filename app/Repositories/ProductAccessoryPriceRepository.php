<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ProductAccessoryPrice;
use App\Traits\Common;

class ProductAccessoryPriceRepository implements Interfaces\iProductAccessoryPrice
{
    use Common;

    public function getById($id)
    {
        try {
            return ProductAccessoryPrice::find($id);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getByIds($product_accessory_id, $product_price_id)
    {
        try {
            return ProductAccessoryPrice::where('product_accessory_id', $product_accessory_id)
                ->where('product_price_id', $product_price_id)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * درج قیمت خرج کار کالا
     * @param $inputs
     * @param $product_price_id
     * @return array
     * @throws ApiException
     */
    public function addProductAccessoryPrice($inputs, $product_price_id): array
    {
        try {
            $product_accessory_price = new ProductAccessoryPrice();

            $product_accessory_price->product_price_id = $product_price_id;
            $product_accessory_price->product_accessory_id = $inputs['product_accessory_id'];
            $product_accessory_price->price = $inputs['price'];

            $result = $product_accessory_price->save();

            return [
                'result' => $result,
                'data' => $result ? $product_accessory_price->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش قیمت خرج کار کالا
     * @param $product_accessory_price
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editProductAccessoryPrice($product_accessory_price, $inputs): mixed
    {
        try {
            $product_accessory_price->price = $inputs['price'];

            return $product_accessory_price->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteByProductPriceId($product_price_id)
    {
        try {
            return ProductAccessoryPrice::where('product_price_id', $product_price_id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteByProductAccessoryId($product_accessory_id)
    {
        try {
            return ProductAccessoryPrice::where('product_accessory_id', $product_accessory_id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
