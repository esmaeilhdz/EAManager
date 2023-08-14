<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPlace;
use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductPrice;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductPriceHelper
{
    use Common;

    // attributes
    public iProduct $product_interface;
    public iProductPrice $product_price_interface;

    public function __construct(
        iProduct $product_interface,
        iProductPrice $product_price_interface
    )
    {
        $this->product_interface = $product_interface;
        $this->product_price_interface = $product_price_interface;
    }

    /**
     * سرویس لیست قیمت های کالا
     * @param $inputs
     * @return array
     */
    public function getProductPrices($inputs): array
    {
        $select = ['id', 'code', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['order_by'] = $this->orderBy($inputs, 'product_prices');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $product_prices = $this->product_price_interface->getProductPrices($product->id, $inputs);

        $product_prices->transform(function ($item) {
            return [
                'id' => $item->id,
                'total_count' => $item->total_count,
                'serial_count' => $item->serial_count,
                'sewing_price' => $item->sewing_price,
                'cutting_price' => $item->cutting_price,
                'sewing_final_price' => $item->sewing_final_price,
                'sale_profit_price' => $item->sale_profit_price,
                'final_price' => $item->final_price,
                'is_enable' => $item->is_enable,
                'creator' => is_null($item->creator->person) ? null : [
                    'person' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_prices,
            'other' => [
                'product_name' => $product->name
            ]
        ];
    }

    /**
     * سرویس جزئیات قیمت کالا
     * @param $inputs
     * @return array
     */
    public function getProductPriceDetail($inputs): array
    {
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // قیمت کالا
        $inputs['product_id'] = $product->id;
        $relation = ['product:id,name'];
        $select = ['product_id', 'total_count', 'serial_count', 'sewing_price', 'cutting_price', 'sewing_final_price', 'sale_profit_price', 'final_price', 'is_enable'];
        $product_price = $this->product_price_interface->getProductPriceById($inputs, $select, $relation);
        if (is_null($product_price)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_price
        ];
    }

    /**
     * سرویس ویرایش قیمت کالا
     * @param $inputs
     * @return array
     */
    public function editProductPrice($inputs): array
    {
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $select = ['id', 'total_count', 'serial_count', 'sewing_price', 'cutting_price', 'sewing_final_price', 'sale_profit_price', 'final_price', 'is_enable'];
        $product_price = $this->product_price_interface->getProductPriceById($inputs, $select);
        if (is_null($product_price)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->product_price_interface->editProductPrice($product_price, $inputs);

        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن قیمت کالا
     * @param $inputs
     * @return array
     */
    public function addProductPrice($inputs): array
    {
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        DB::beginTransaction();
        $inputs['product_id'] = $product->id;
        $user = Auth::user();
        $result[] = $this->product_price_interface->deActiveOldPrices($product->id);
        $result[] = $this->product_price_interface->addProductPrice($inputs, $user);

        $result = $this->prepareTransactionArray($result);

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس حذف قیمت کالا
     * @param $inputs
     * @return array
     */
    public function deleteProductPrice($inputs): array
    {
        // کالا
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // قیمت کالا
        $inputs['product_id'] = $product->id;
        $product_price = $this->product_price_interface->getProductPriceById($inputs, ['id']);
        if (is_null($product_price)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->product_price_interface->deleteProductPrice($product_price);

        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
