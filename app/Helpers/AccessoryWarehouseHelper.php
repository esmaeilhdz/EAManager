<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iAccessory;
use App\Repositories\Interfaces\iAccessoryWarehouse;
use App\Traits\Common;

class AccessoryWarehouseHelper
{
    use Common;

    // attributes
    public iAccessory $accessory_interface;
    public iAccessoryWarehouse $accessory_warehouse_interface;

    public function __construct(
        iAccessory $accessory_interface,
        iAccessoryWarehouse $accessory_warehouse_interface
    )
    {
        $this->accessory_interface = $accessory_interface;
        $this->accessory_warehouse_interface = $accessory_warehouse_interface;
    }

    /**
     * لیست انبار خرج کار ها
     * @param $inputs
     * @return array
     */
    public function getAccessoryWarehouses($inputs): array
    {
        $accessory = $this->accessory_interface->getAccessoryById($inputs['accessory_id']);
        if (is_null($accessory)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['order_by'] = $this->orderBy($inputs, 'accessory_warehouses');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $accessor_warehouses = $this->accessory_warehouse_interface->getAccessoryWarehouses($inputs);

        $accessor_warehouses->transform(function ($item) {
            return [
                'id' => $item->id,
                'place' => is_null($item->place_id) ? null : [
                    'id' => $item->place_id,
                    'name' => $item->place->name
                ],
                'count' => $item->count,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $accessor_warehouses
        ];
    }

}
