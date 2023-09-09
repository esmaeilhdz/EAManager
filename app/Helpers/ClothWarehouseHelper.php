<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iClothBuy;
use App\Repositories\Interfaces\iClothWarehouse;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClothWarehouseHelper
{
    use Common;

    // attributes
    public iClothWarehouse $cloth_warehouse_interface;
    public iCloth $cloth_interface;

    public function __construct(
        iClothWarehouse $cloth_warehouse_interface,
        iCloth $cloth_interface
    )
    {
        $this->cloth_warehouse_interface = $cloth_warehouse_interface;
        $this->cloth_interface = $cloth_interface;
    }

    /**
     * لیست انبارهای پارچه
     * @param $inputs
     * @return array
     */
    public function getClothWarehouses($inputs): array
    {
        $cloth = $this->cloth_interface->getClothByCode($inputs['code']);
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['place']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['place']['params'] = $param_array;

        $inputs['cloth_id'] = $cloth->id;
        $inputs['order_by'] = $this->orderBy($inputs, 'cloth_warehouses');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $cloth_warehouses = $this->cloth_warehouse_interface->getClothWarehouses($inputs);

        $cloth_warehouses->transform(function ($item) {
            return [
                'place' => [
                    $item->place->name
                ],
                'metre' => $item->metre,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $cloth_warehouses,
            'other' => [
                'cloth_name' => $cloth->name
            ]
        ];
    }


}
