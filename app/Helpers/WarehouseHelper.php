<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPerson;
use App\Repositories\Interfaces\iRole;
use App\Repositories\Interfaces\iWarehouse;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseHelper
{
    use Common;

    // attributes
    public iWarehouse $warehouse_interface;

    public function __construct(iWarehouse $warehouse_interface)
    {
        $this->warehouse_interface = $warehouse_interface;
    }

    /**
     * سرویس لیست افراد
     * @param $inputs
     * @return array
     */
    public function getWarehouses($inputs): array
    {
        $user = Auth::user();

        $inputs['order_by'] = $this->orderBy($inputs, 'Warehouses');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $Warehouses = $this->warehouse_interface->getWarehouses($inputs, $user);

        $Warehouses->transform(function ($item) {
            return [
                'code' => $item->code,
                'parent' => is_null($item->parent_id) ? null : [
                    'code' => $item->parent->code,
                    'name' => $item->parent->name
                ],
                'name' => $item->name,
                'is_enable' => $item->is_enable,
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $Warehouses
        ];
    }

    /**
     * سرویس جزئیات انبار
     * @param $code
     * @return array
     */
    public function getWarehouseDetail($code): array
    {
        $user = Auth::user();
        $warehouse = $this->warehouse_interface->getWarehouseByCode($code, $user);
        if (is_null($warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $warehouse
        ];
    }

    public function getWarehousesCombo($inputs)
    {
        $user = Auth::user();
        $warehouses = $this->warehouse_interface->getWarehousesCombo($inputs, $user);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $warehouses
        ];

    }

    /**
     * سرویس ویرایش انبار
     * @param $inputs
     * @return array
     */
    public function editWarehouse($inputs): array
    {
        $user = Auth::user();
        $warehouse = $this->warehouse_interface->getWarehouseByCode($inputs['code'], $user);
        if (is_null($warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if(isset($inputs['parent_code'])) {
            $parent_warehouse = $this->warehouse_interface->getWarehouseByCode($inputs['parent_code'], $user, ['id']);
            if (is_null($parent_warehouse)) {
                return [
                    'result' => false,
                    'message' => __('messages.record_not_found'),
                    'data' => null
                ];
            }

            if ($warehouse->id == $parent_warehouse->id) {
                return [
                    'result' => false,
                    'message' => __('messages.data_is_incorrect'),
                    'data' => null
                ];
            }
        }
        $inputs['parent_id'] = $parent_warehouse->id ?? null;

        $result = $this->warehouse_interface->editWarehouse($inputs, $warehouse);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن انبار
     * @param $inputs
     * @return array
     */
    public function addWarehouse($inputs): array
    {
        $user = Auth::user();
        if(isset($inputs['parent_code'])) {
            $parent_warehouse = $this->warehouse_interface->getWarehouseByCode($inputs['parent_code'], $user, ['id']);
            if (is_null($parent_warehouse)) {
                return [
                    'result' => false,
                    'message' => __('messages.record_not_found'),
                    'data' => null
                ];
            }
        }
        $inputs['parent_id'] = $parent_warehouse->id ?? null;
        $result = $this->warehouse_interface->addWarehouse($inputs, $user);

        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['result'] ? $result['data']->code : null
        ];
    }

    /**
     * سرویس حذف انبار
     * @param $inputs
     * @return array
     */
    public function deleteWarehouse($inputs): array
    {
        $user = Auth::user();
        $warehouse = $this->warehouse_interface->getWarehouseByCode($inputs['code'], $user, ['id']);
        if (is_null($warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->warehouse_interface->deleteWarehouse($warehouse);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
