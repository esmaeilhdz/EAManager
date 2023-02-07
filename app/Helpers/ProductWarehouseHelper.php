<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductWarehouseHelper
{
    use Common;

    // attributes
    public iProduct $product_interface;
    public iProductWarehouse $product_warehouse_interface;

    public function __construct(
        iProduct $product_interface,
        iProductWarehouse $product_warehouse_interface
    )
    {
        $this->product_interface = $product_interface;
        $this->product_warehouse_interface = $product_warehouse_interface;
    }

    /**
     * سرویس لیست قیمت های کالا
     * @param $inputs
     * @return array
     */
    public function getProductWarehouses($inputs): array
    {
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['order_by'] = $this->orderBy($inputs, 'product_warehouses');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $user = Auth::user();
        $product_warehouses = $this->product_warehouse_interface->getProductWarehouses($product->id, $inputs, $user);

        $product_warehouses->transform(function ($item) {
            return [
                'id' => $item->id,
                'place' => [
                    'id' => $item->place_id,
                    'name' => $item->place->name
                ],
                'free_size_count' => $item->free_size_count,
                'size1_count' => $item->size1_count,
                'size2_count' => $item->size2_count,
                'size3_count' => $item->size3_count,
                'size4_count' => $item->size4_count,
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
            'data' => $product_warehouses,
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
    public function getProductWarehouseDetail($inputs): array
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

        // انبار کالا
        $inputs['product_id'] = $product->id;
        $relation = ['color:enum_id,enum_caption'];
        $select = ['product_id', 'place_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count', 'is_enable'];
        $product_warehouse = $this->product_warehouse_interface->getProductWarehouseById($inputs, $select, $relation);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_warehouse,
            'other' => [
                'product_name' => $product->name
            ]
        ];
    }

    /**
     * سرویس ویرایش قیمت کالا
     * @param $inputs
     * @return array
     */
    public function editProductWarehouse($inputs): array
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
        $select = ['id', 'product_id', 'place_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count', 'is_enable'];
        $product_warehouse = $this->product_warehouse_interface->getProductWarehouseById($inputs, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->product_warehouse_interface->editProductWarehouse($product_warehouse, $inputs);

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
    public function addProductWarehouse($inputs): array
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
        $result[] = $this->product_warehouse_interface->deActiveOldWarehouses($inputs);
        $res = $this->product_warehouse_interface->addProductWarehouse($inputs, $user);
        $result[] = $res['result'];

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
            'data' => $res['data']
        ];
    }

    /**
     * سرویس حذف قیمت کالا
     * @param $inputs
     * @return array
     */
    public function deleteProductWarehouse($inputs): array
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

        // انبار کالا
        $inputs['product_id'] = $product->id;
        $product_warehouse = $this->product_warehouse_interface->getProductWarehouseById($inputs, ['id']);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->product_warehouse_interface->deleteProductWarehouse($product_warehouse);

        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}