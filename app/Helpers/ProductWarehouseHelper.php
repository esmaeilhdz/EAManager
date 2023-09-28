<?php

namespace App\Helpers;

use App\Models\ProductWarehouse;
use App\Repositories\Interfaces\iPlace;
use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductModel;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductWarehouseHelper
{
    use Common;

    // attributes
    public iPlace $place_interface;
    public iProduct $product_interface;
    public iProductModel $product_model_interface;
    public iProductWarehouse $product_warehouse_interface;

    public function __construct(
        iPlace $place_interface,
        iProduct $product_interface,
        iProductModel $product_model_interface,
        iProductWarehouse $product_warehouse_interface
    )
    {
        $this->place_interface = $place_interface;
        $this->product_interface = $product_interface;
        $this->product_model_interface = $product_model_interface;
        $this->product_warehouse_interface = $product_warehouse_interface;
    }

    /**
     * سرویس لیست انبار های کالا
     * @param $inputs
     * @return array
     */
    public function getProductWarehouses($inputs): array
    {
        $user = Auth::user();
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['order_by'] = $this->orderBy($inputs, 'product_warehouses');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $product_warehouses = $this->product_warehouse_interface->getProductWarehouses($product->id, $inputs, $user);

        $product_warehouses->transform(function ($item) {
            return [
                'id' => $item->id,
                'place' => [
                    'id' => $item->place_id,
                    'name' => $item->place->name
                ],
                'product_model' => [
                    'id' => $item->product_model_id,
                    'name' => $item->product_model->name
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
     * سرویس جزئیات انبار کالا
     * @param $inputs
     * @return array
     */
    public function getProductWarehouseDetail($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // انبار کالا
        $inputs['product_id'] = $product->id;
        $relation = [
            'product_model:id,product_id,name',
            'place:id,name',
        ];
        $select = ['product_id', 'product_model_id', 'place_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count', 'is_enable'];
        $product_warehouse = $this->product_warehouse_interface->getProductWarehouseById($inputs, $user, $select, $relation);
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
     * @param $inputs
     * @return array
     */
    public function getProductsOfPlace($inputs): array
    {
        $place = $this->place_interface->getPlaceById($inputs['id']);
        if (!$place) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $user = Auth::user();
        $product_warehouses = $this->product_warehouse_interface->getByPlaceId($inputs, $user);

        $product_warehouses->transform(function ($item) {
            return [
                'id' => $item->id,
                'caption' => $item->product->name . ' - ' . $item->product_model->name
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_warehouses
        ];
    }

    /**
     * @param $inputs
     * @return array
     */
    public function getProductWarehouseCombo($inputs): array
    {
        $user = Auth::user();
        $product_warehouses = $this->product_warehouse_interface->getProductWarehouseCombo($inputs, $user);

        $product_warehouses->transform(function ($item) {
            return [
                'id' => $item->id,
                'caption' => $item->product->name . ' - ' . $item->product_model->name . ' (' . $item->place->name . ')'
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_warehouses
        ];
    }

    /**
     * سرویس ویرایش انبار کالا
     * @param $inputs
     * @return array
     */
    public function editProductWarehouse($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $product_model = $this->product_model_interface->getById($product->id, $inputs['product_model_id'], $user);
        if (is_null($product_model)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $select = ['id', 'product_id', 'place_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count', 'is_enable'];
        $product_warehouse = $this->product_warehouse_interface->getProductWarehouseById($inputs, $user, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        DB::beginTransaction();
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
            'data' => null
        ];
    }

    /**
     * سرویس افزودن انبار کالا
     * @param $inputs
     * @return array
     */
    public function addProductWarehouse($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $product_model = $this->product_model_interface->getById($product->id, $inputs['product_model_id'], $user);
        if (is_null($product_model)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $product_warehouse = $this->product_warehouse_interface->getByProductAndPlace($inputs['place_id'], $product->id, $inputs['product_model_id'], $user);
        if ($product_warehouse) {
            return [
                'result' => false,
                'message' => __('messages.product_warehouse_already_exists'),
                'data' => null
            ];
        }

        DB::beginTransaction();
        $result[] = $this->product_warehouse_interface->deActiveOldWarehouses($inputs);
        $res = $this->product_warehouse_interface->addProductWarehouse($inputs, $user);
        $result[] = $res['result'];

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
            'data' => $res['data']
        ];
    }

    /**
     * سرویس حذف انبار کالا
     * @param $inputs
     * @return array
     */
    public function deleteProductWarehouse($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
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

    /**
     * @param ProductWarehouse $product_warehouse
     * @param array $data
     * @return array
     */
    public function checkStock(ProductWarehouse $product_warehouse, array $data): array
    {
        $flag = true;
        $message = null;
        $size = null;
        if ($product_warehouse->free_size_count - $data['free_size_count'] < 0) {
            $flag = false;
            $message[] = __('messages.free_size_not_enough');
            $size[] = 'free_size_count';
        }

        if ($product_warehouse->size1_count - $data['size1_count'] < 0) {
            $flag = false;
            $message[] = __('messages.size1_not_enough');
            $size[] = 'size1_count';
        }

        if ($product_warehouse->size2_count - $data['size2_count'] < 0) {
            $flag = false;
            $message[] = __('messages.size2_not_enough');
            $size[] = 'size2_count';
        }

        if ($product_warehouse->size3_count - $data['size3_count'] < 0) {
            $flag = false;
            $message[] = __('messages.size3_not_enough');
            $size[] = 'size3_count';
        }

        if ($product_warehouse->size4_count - $data['size4_count'] < 0) {
            $flag = false;
            $message[] = __('messages.size4_not_enough');
            $size[] = 'size4_count';
        }

        return [
            'result' => $flag,
            'message' => $message,
            'data' => $flag ? null : $product_warehouse->product->name,
            'size' => $size
        ];

    }

}
