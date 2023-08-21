<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iClothSell;
use App\Repositories\Interfaces\iClothWarehouse;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClothSellHelper
{
    use Common;

    // attributes
    public iClothWarehouse $cloth_warehouse_interface;
    public iClothSell $cloth_sell_interface;
    public iCloth $cloth_interface;

    public function __construct(
        iClothWarehouse $cloth_warehouse_interface,
        iClothSell $cloth_sell_interface,
        iCloth $cloth_interface
    )
    {
        $this->cloth_warehouse_interface = $cloth_warehouse_interface;
        $this->cloth_sell_interface = $cloth_sell_interface;
        $this->cloth_interface = $cloth_interface;
    }

    /**
     * لیست فروش پارچه
     * @param $inputs
     * @return array
     */
    public function getClothSells($inputs): array
    {
        $cloth = $this->cloth_interface->getClothByCode($inputs['code']);
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['cloth_id'] = $cloth->id;
        $inputs['order_by'] = $this->orderBy($inputs, 'cloth_sells');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $clothes = $this->cloth_sell_interface->getClothSells($inputs);

        $clothes->transform(function ($item) {
            return [
                'id' => $item->id,
                'customer' => $item->customer->name,
                'warehouse_place' => $item->warehouse_place->name,
                'metre' => $item->metre,
                'roll_count' => $item->roll_count,
                'receive_date' => $item->receive_date,
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
            'data' => $clothes
        ];
    }

    /**
     * جزئیات فروش پارچه
     * @param $inputs
     * @return array
     */
    public function getClothSellDetail($inputs): array
    {
        $cloth = $this->cloth_interface->getClothByCode($inputs['code']);
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['cloth_id'] = $cloth->id;
        $cloth_sell = $this->cloth_sell_interface->getClothSellById($inputs);
        if (is_null($cloth_sell)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $cloth_sell
        ];
    }

    /**
     * ویرایش فروش پارچه
     * @param $inputs
     * @return array
     */
    public function editClothSell($inputs): array
    {
        $cloth = $this->cloth_interface->getClothByCode($inputs['code']);
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }


        $inputs['cloth_id'] = $cloth->id;
        $params['cloth_id'] = $cloth->id;
        $params = array_merge($params, $inputs);
        $cloth_sell = $this->cloth_sell_interface->getClothSellById($inputs);
        if (is_null($cloth_sell)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $cloth_warehouse = $this->cloth_warehouse_interface->getClothWarehousesByCloth($cloth->id, $inputs['warehouse_place_id']);
        if (!$cloth_warehouse) {
            return [
                'result' => false,
                'message' => __('messages.warehouse_not_exists'),
                'data' => null
            ];
        }

        /*if (
            $cloth_warehouse->metre - $inputs['metre'] < 0 ||
            $cloth_warehouse->roll_count - $inputs['roll_count'] < 0
        ) {
            return [
                'result' => false,
                'message' => __('messages.not_enough_warehouse_stock'),
                'data' => null
            ];
        }*/

        DB::beginTransaction();
        if ($cloth_sell->metre > $inputs['metre']) {
            $params['sign'] = 'plus';
            $params['metre'] = $inputs['metre'];
        } elseif ($cloth_sell->metre < $inputs['metre']) {
            $params['sign'] = 'minus';
            $params['metre'] = $inputs['metre'];
        } else {
            $params['sign'] = 'equal';
            $params['metre'] = $inputs['metre'];
        }
        $result[] = $this->cloth_warehouse_interface->editWarehouseMetre($params);

        if ($cloth_sell->roll_count > $inputs['roll_count']) {
            $params['sign'] = 'plus';
            $params['roll_count'] = $inputs['roll_count'];
        } elseif ($cloth_sell->roll_count < $inputs['roll_count']) {
            $params['sign'] = 'minus';
            $params['roll_count'] = $inputs['roll_count'];
        } else {
            $params['sign'] = 'equal';
            $params['roll_count'] = $inputs['roll_count'];
        }

        $result[] = $this->cloth_sell_interface->editClothSell($cloth_sell, $inputs);
        $result[] = $this->cloth_warehouse_interface->editWarehouseRollCount($params);

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
     * افزودن فروش پارچه
     * @param $inputs
     * @return array
     */
    public function addClothSell($inputs): array
    {
        $user = Auth::user();
        $cloth = $this->cloth_interface->getClothByCode($inputs['code']);
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $cloth_warehouse = $this->cloth_warehouse_interface->getClothWarehousesByCloth($cloth->id, $inputs['warehouse_place_id']);
        if (!$cloth_warehouse) {
            return [
                'result' => false,
                'message' => __('messages.warehouse_not_exists'),
                'data' => null
            ];
        }

        if (
            $cloth_warehouse->metre - $inputs['metre'] < 0 ||
            $cloth_warehouse->roll_count - $inputs['roll_count'] < 0
        ) {
            return [
                'result' => false,
                'message' => __('messages.not_enough_warehouse_stock'),
                'data' => null
            ];
        }

        $inputs['cloth_id'] = $cloth->id;
        $result = $this->cloth_sell_interface->addClothSell($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * حذف فروش پارچه
     * @param $inputs
     * @return array
     */
    public function deleteClothSell($inputs): array
    {
        $cloth = $this->cloth_interface->getClothByCode($inputs['code']);
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['cloth_id'] = $cloth->id;
        $cloth_sell = $this->cloth_sell_interface->getClothSellById($inputs);
        if (is_null($cloth_sell)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->cloth_sell_interface->deleteClothSell($cloth_sell);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}