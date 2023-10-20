<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPerson;
use App\Repositories\Interfaces\iRole;
use App\Repositories\Interfaces\iWarehouse;
use App\Repositories\Interfaces\iWarehouseItem;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseItemHelper
{
    use Common;

    // attributes
    public iWarehouse $warehouse_interface;
    public iWarehouseItem $warehouse_item_interface;

    public function __construct(
        iWarehouse $warehouse_interface,
        iWarehouseItem $warehouse_item_interface
    )
    {
        $this->warehouse_interface = $warehouse_interface;
        $this->warehouse_item_interface = $warehouse_item_interface;
    }

    public function getWarehouseItemByData($inputs, $user)
    {
        $warehouse_item = $this->warehouse_item_interface->getWarehouseItemByData($inputs, $user);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $warehouse_item
        ];
    }

}
