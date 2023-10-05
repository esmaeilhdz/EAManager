<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iAccessory;
use App\Repositories\Interfaces\iAccessoryBuy;
use App\Repositories\Interfaces\iAccessoryWarehouse;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccessoryBuyHelper
{
    use Common;

    // attributes
    public iAccessory $accessory_interface;
    public iAccessoryBuy $accessory_buy_interface;
    public iAccessoryWarehouse $accessory_warehouse_interface;

    public function __construct(
        iAccessory $accessory_interface,
        iAccessoryBuy $accessory_buy_interface,
        iAccessoryWarehouse $accessory_warehouse_interface
    )
    {
        $this->accessory_interface = $accessory_interface;
        $this->accessory_buy_interface = $accessory_buy_interface;
        $this->accessory_warehouse_interface = $accessory_warehouse_interface;
    }

    /**
     * لیست خرید خرج کار ها
     * @param $inputs
     * @return array
     */
    public function getAccessoryBuys($inputs): array
    {
        $accessory = $this->accessory_interface->getAccessoryById($inputs['accessory_id']);
        if (is_null($accessory)) {
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

        $inputs['order_by'] = $this->orderBy($inputs, 'accessory_buys');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $accessor_buys = $this->accessory_buy_interface->getAccessoryBuys($inputs);

        $accessor_buys->transform(function ($item) {
            return [
                'id' => $item->id,
                'place' => [
                    'id' => $item->place_id,
                    'name' => $item->place->name
                ],
                'count' => $item->count,
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
            'data' => $accessor_buys
        ];
    }

    /**
     * جزئیات خرید خرج کار
     * @param $inputs
     * @return array
     */
    public function getAccessoryBuyDetail($inputs): array
    {
        $accessory = $this->accessory_interface->getAccessoryById($inputs['accessory_id']);
        if (is_null($accessory)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $accessory_buy = $this->accessory_buy_interface->getAccessoryBuyById($inputs['id']);
        if (is_null($accessory_buy)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $accessory_buy
        ];
    }

    /**
     * ویرایش خرید خرج کار
     * @param $inputs
     * @return array
     */
    public function editAccessoryBuy($inputs): array
    {
        $accessory = $this->accessory_interface->getAccessoryById($inputs['accessory_id']);
        if (is_null($accessory)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $accessory_buy = $this->accessory_buy_interface->getAccessoryBuyById($inputs);
        if (is_null($accessory_buy)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $params['accessory_id'] = $accessory->id;
        if ($accessory_buy->count > $inputs['count']) {
            $params['sign'] = 'minus';
            $params['count'] = $accessory_buy->count - $inputs['count'];
        } elseif ($accessory_buy->count < $inputs['count']) {
            $params['sign'] = 'plus';
            $params['count'] = $inputs['count'] - $accessory_buy->count;
        } else {
            $params['sign'] = 'equal';
            $params['count'] = $inputs['count'];
        }

        DB::beginTransaction();
        $result[] = $this->accessory_buy_interface->editAccessoryBuy($accessory_buy, $inputs);
        $result[] = $this->accessory_warehouse_interface->editAccessoryWarehouse($params);

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
     * افزودن خرید خرج کار
     * @param $inputs
     * @return array
     */
    public function addAccessoryBuy($inputs): array
    {
        $accessory = $this->accessory_interface->getAccessoryById($inputs['accessory_id']);
        if (is_null($accessory)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $user = Auth::user();
        $result = $this->accessory_buy_interface->addAccessoryBuy($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * حذف خرید خرج کار
     * @param $inputs
     * @return array
     */
    public function deleteAccessoryBuy($inputs): array
    {
        $accessory = $this->accessory_interface->getAccessoryById($inputs['accessory_id']);
        if (is_null($accessory)) {
            return [
                'result' => false,
                'message' => __('messages.accessory_not_found'),
                'data' => null
            ];
        }

        $accessory_buy = $this->accessory_buy_interface->getAccessoryBuyById($inputs);
        if (is_null($accessory_buy)) {
            return [
                'result' => false,
                'message' => __('messages.accessory_buy_not_found'),
                'data' => null
            ];
        }

        $result = $this->accessory_buy_interface->deleteAccessoryBuy($accessory_buy);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
