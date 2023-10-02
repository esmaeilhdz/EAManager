<?php

namespace App\Helpers;

use App\Models\Accessory;
use App\Models\Cloth;
use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductAccessory;
use App\Repositories\Interfaces\iSalePeriod;
use App\Traits\Common;
use App\Traits\ProductAccessoryTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductHelper
{
    use Common, ProductAccessoryTrait;

    // attributes
    public iProduct $product_interface;
    public iCloth $cloth_interface;
    public iSalePeriod $sale_period_interface;
    public iProductAccessory $product_accessory_interface;

    public function __construct(
        iProduct $product_interface,
        iCloth $cloth_interface,
        iSalePeriod $sale_period_interface,
        iProductAccessory $product_accessory_interface
    )
    {
        $this->product_interface = $product_interface;
        $this->cloth_interface = $cloth_interface;
        $this->sale_period_interface = $sale_period_interface;
        $this->product_accessory_interface = $product_accessory_interface;
    }

    /**
     * سرویس لیست کالا ها
     * @param $inputs
     * @return array
     */
    public function getProducts($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $products = $this->product_interface->getProducts($inputs);

        $products->transform(function ($item) {
            $warehouse_count = 0;
            if (!is_null($item->productWarehouse)) {
                $warehouse_count = $item->productWarehouse->free_size_count + $item->productWarehouse->size1_count + $item->productWarehouse->size2_count + $item->productWarehouse->size3_count + $item->productWarehouse->size4_count;
            }
            return [
                'code' => $item->code,
                'name' => $item->name,
                'internal_code' => $item->internal_code,
                'product_warehouse_count' => $warehouse_count,
                'price' => $item->productPrice->final_price ?? null,
                'cloth' => [
                    'name' => $item->cloth->name,
                ],
                'sale_period' => [
                    'name' => $item->sale_period->name,
                ],
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
            'data' => $products
        ];
    }

    /**
     * سرویس جزئیات کالا
     * @param $code
     * @return array
     */
    public function getProductDetail($code): array
    {
        $select = [
            'code', 'internal_code', 'name', 'cloth_id', 'sale_period_id'
        ];
        $user = Auth::user();
        $product = $this->product_interface->getProductByCode($code, $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product
        ];
    }

    /**
     * سرویس کامبوی کالا
     * @param $inputs
     * @return array
     */
    public function getProductCombo($inputs): array
    {
        $user = Auth::user();
        $products = $this->product_interface->getProductsCombo($inputs, $user);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $products
        ];
    }

    /**
     * سرویس ویرایش کالا
     * @param $inputs
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function editProduct($inputs): array
    {
        $user = Auth::user();
        $product = $this->product_interface->getProductByCode($inputs['code'], $user);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.product_not_found'),
                'data' => null
            ];
        }

        $cloth = $this->cloth_interface->getClothByCode($inputs['cloth_code'], $user);
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.cloth_not_found'),
                'data' => null
            ];
        }

        $sale_period = $this->sale_period_interface->getSalePeriodById($inputs['sale_period_id']);
        if (is_null($sale_period)) {
            return [
                'result' => false,
                'message' => __('messages.sale_period_not_found'),
                'data' => null
            ];
        }

        $inputs['cloth_id'] = $cloth->id;

        $transaction_product_accessories = $this->getInsertableAccessories($product->id, $inputs, $user);
        $inserts = $transaction_product_accessories['inserts'];
        $deletes = $transaction_product_accessories['deletes'];
        $updates = $transaction_product_accessories['updates'];

        DB::beginTransaction();
        $result[] = $this->product_interface->editProduct($product, $inputs);
        foreach ($inserts as $insert) {
            $result[] = $this->product_accessory_interface->addProductAccessory($insert, $user);
        }

        foreach ($updates as $update) {
            $result[] = $this->product_accessory_interface->editProductAccessoryByData($product->id, $update);
        }

        foreach ($deletes as $delete) {
            $result[] = $this->product_accessory_interface->deleteProductAccessoriesByData($product->id, $delete);
        }

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
     * سرویس افزودن کالا
     * @param $inputs
     * @return array
     */
    public function addProduct($inputs): array
    {
        $user = Auth::user();
        $cloth = $this->cloth_interface->getClothByCode($inputs['cloth_code'], $user);
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $sale_period = $this->sale_period_interface->getSalePeriodById($inputs['sale_period_id']);
        if (is_null($sale_period)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['cloth_id'] = $cloth->id;
        $result = $this->product_interface->addProduct($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * سرویس حذف کالا
     * @param $inputs
     * @return array
     */
    public function deleteProduct($inputs): array
    {
        $user = Auth::user();
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, ['id', 'code']);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->product_interface->deleteProduct($product);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
