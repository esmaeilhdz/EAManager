<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iClothBuy;
use App\Repositories\Interfaces\iClothBuyItems;
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
    public iClothBuyItems $cloth_buy_item_interface;

    public function __construct(
        iClothWarehouse $cloth_warehouse_interface,
        iClothBuy $cloth_buy_interface,
        iCloth $cloth_interface,
        iClothBuyItems $cloth_buy_item_interface,
    )
    {
        $this->cloth_warehouse_interface = $cloth_warehouse_interface;
        $this->cloth_buy_interface = $cloth_buy_interface;
        $this->cloth_interface = $cloth_interface;
        $this->cloth_buy_item_interface = $cloth_buy_item_interface;
    }

    /**
     * لیست خرید پارچه
     * @param $inputs
     * @return array
     */
    public function getClothBuys($inputs): array
    {
        $user = Auth::user();
        $cloth = $this->cloth_interface->getClothByCode($inputs['code'], $user);
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
        $inputs['order_by'] = $this->orderBy($inputs, 'cloth_buys');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $clothes = $this->cloth_buy_interface->getClothBuys($inputs);

        $clothes->transform(function ($item) {
            return [
                'id' => $item->id,
                'seller_place' => $item->seller_place->name,
                'warehouse_place' => $item->warehouse_place->name,
                'receive_date' => $item->receive_date,
                'factor_no' => $item->factor_no,
                'price' => $item->price,
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
        $user = Auth::user();
        $cloth = $this->cloth_interface->getClothByCode($inputs['code'], $user);
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
        $user = Auth::user();
        $cloth = $this->cloth_interface->getClothByCode($inputs['code'], $user);
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

        DB::beginTransaction();
        $result[] = $this->cloth_buy_interface->editClothBuy($cloth_buy, $inputs);
        $result[] = $this->cloth_buy_item_interface->deleteClothBuyItems($cloth_buy->id);
        foreach ($inputs['items'] as $item) {
            $item['cloth_buy_id'] = $cloth_buy->id;
            $item['color_id'] = $item->color_id;

            $cloth_warehouse = $this->cloth_warehouse_interface->getClothWarehousesByCloth($cloth->id, $item->color_id, $inputs['warehouse_place_id']);
            if (is_null($cloth_warehouse)) {
                DB::rollBack();
                return [
                    'result' => false,
                    'message' => __('messages.cloth_warehouse_not_found'),
                    'data' => null
                ];
            }

            if ($cloth_warehouse->metre > $item['metre']) {
                $params['sign'] = 'minus';
                $params['metre'] = $cloth_warehouse->metre - $item['metre'];
            } elseif ($cloth_warehouse->metre < $item['metre']) {
                $params['sign'] = 'plus';
                $params['metre'] = $item['metre'] - $cloth_warehouse->metre;
            } else {
                $params['sign'] = 'equal';
                $params['metre'] = $item['metre'];
            }

            $result[] = $this->cloth_buy_item_interface->addClothBuyItem($item, $user);
            $result[] = $this->cloth_warehouse_interface->editWarehouse($params);
        }

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
        $cloth = $this->cloth_interface->getClothByCode($inputs['code'], $user);
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        DB::beginTransaction();
        $inputs['cloth_id'] = $cloth->id;
        $res = $this->cloth_buy_interface->addClothBuy($inputs, $user);
        $result[] = $res['result'];
        foreach ($inputs['items'] as $item) {
            $item['cloth_buy_id'] = $res['data'];
            $result[] = $this->cloth_buy_item_interface->addClothBuyItem($item, $user);
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
     * حذف خرید پارچه
     * @param $inputs
     * @return array
     */
    public function deleteClothBuy($inputs): array
    {
        $user = Auth::user();
        $cloth = $this->cloth_interface->getClothByCode($inputs['code'], $user);
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

        DB::beginTransaction();
        $result[] = $this->cloth_buy_item_interface->deleteClothBuyItems($cloth_buy->id);
        $result[] = $this->cloth_buy_interface->deleteClothBuy($cloth_buy);

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

}
