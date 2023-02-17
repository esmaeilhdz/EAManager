<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iClothBuy;
use App\Repositories\Interfaces\iClothWarehouse;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClothBuyHelper
{
    use Common;

    // attributes
    public iClothWarehouse $cloth_warehouse_interface;
    public iClothBuy $cloth_buy_interface;
    public iCloth $cloth_interface;

    public function __construct(
        iClothWarehouse $cloth_warehouse_interface,
        iClothBuy $cloth_buy_interface,
        iCloth $cloth_interface
    )
    {
        $this->cloth_warehouse_interface = $cloth_warehouse_interface;
        $this->cloth_buy_interface = $cloth_buy_interface;
        $this->cloth_interface = $cloth_interface;
    }

    /**
     * لیست خرید پارچه
     * @param $inputs
     * @return array
     */
    public function getClothBuys($inputs): array
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
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $clothes = $this->cloth_buy_interface->getClothBuys($inputs);

        $clothes->transform(function ($item) {
            return [
                'place' => $item->place->name,
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
     * جزئیات خرید پارچه
     * @param $inputs
     * @return array
     */
    public function getClothBuyDetail($inputs): array
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
        $cloth_buy = $this->cloth_buy_interface->getClothBuyById($inputs);
        if (is_null($cloth_buy)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $cloth_buy
        ];
    }

    /**
     * ویرایش خرید پارچه
     * @param $inputs
     * @return array
     */
    public function editClothBuy($inputs): array
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
        $cloth_buy = $this->cloth_buy_interface->getClothBuyById($inputs);
        if (is_null($cloth_buy)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if ($cloth_buy->metre > $inputs['metre']) {
            $params['sign'] = 'minus';
            $params['metre'] = $cloth_buy->metre - $inputs['metre'];
        } elseif ($cloth_buy->metre < $inputs['metre']) {
            $params['sign'] = 'plus';
            $params['metre'] = $inputs['metre'] - $cloth_buy->metre;
        } else {
            $params['sign'] = 'equal';
            $params['metre'] = $inputs['metre'];
        }

        if ($cloth_buy->roll_count > $inputs['roll_count']) {
            $params['roll_count'] = $cloth_buy->roll_count - $inputs['roll_count'];
        } elseif ($cloth_buy->roll_count < $inputs['roll_count']) {
            $params['roll_count'] = $inputs['roll_count'] - $cloth_buy->roll_count;
        } else {
            $params['sign'] = 'equal';
            $params['roll_count'] = $inputs['roll_count'];
        }

        DB::beginTransaction();
        $result[] = $this->cloth_buy_interface->editClothBuy($cloth_buy, $inputs);
        $result[] = $this->cloth_warehouse_interface->editWarehouse($params);

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
     * افزودن خرید پارچه
     * @param $inputs
     * @return array
     */
    public function addClothBuy($inputs): array
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

        $inputs['cloth_id'] = $cloth->id;
        $result = $this->cloth_buy_interface->addClothBuy($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * حذف خرید پارچه
     * @param $inputs
     * @return array
     */
    public function deleteClothBuy($inputs): array
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
        $cloth_buy = $this->cloth_buy_interface->getClothBuyById($inputs);
        if (is_null($cloth_buy)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->cloth_buy_interface->deleteClothBuy($cloth_buy);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
