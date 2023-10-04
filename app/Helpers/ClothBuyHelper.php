<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iClothBuy;
use App\Repositories\Interfaces\iClothBuyItems;
use App\Repositories\Interfaces\iClothWarehouse;
use App\Traits\ClothBuyTrait;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClothBuyHelper
{
    use Common, ClothBuyTrait;

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
                'items' => $item->items,
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

        $items = null;
        foreach ($cloth_buy->items as $item) {
            $items[] = [
                'id' => $item->id,
                'color' => [
                    'id' => $item->color->enum_id,
                    'caption' => $item->color->enum_caption,
                ],
                'metre' => $item->metre,
                'unit_price' => $item->unit_price,
                'price' => $item->price,
            ];
        }

        $result = [
            'id' => $cloth_buy->id,
            'cloth' => [
                'code' => $cloth->code,
                'name' => $cloth->name
            ],
            'seller_place' => [
                'id' => $cloth_buy->seller_place_id,
                'name' => $cloth_buy->seller_place->name
            ],
            'warehouse_place' => [
                'id' => $cloth_buy->warehouse_place_id,
                'name' => $cloth_buy->warehouse_place->name
            ],
            'factor_no' => $cloth_buy->factor_no,
            'price' => (string) $cloth_buy->price,
            'receive_date' => $cloth_buy->receive_date,
            'items' => $items
        ];

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $result
        ];
    }

    /**
     * ویرایش خرید پارچه
     * @param $inputs
     * @return array
     * @throws \App\Exceptions\ApiException
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

        $cloth_buy_transaction = $this->getItemsTransaction($cloth_buy->id, $inputs, $user);
        $inserts = $cloth_buy_transaction['insert'];
        $deletes = $cloth_buy_transaction['delete'];
        $updates = $cloth_buy_transaction['update'];

        DB::beginTransaction();

        $result[] = $this->cloth_buy_interface->editClothBuy($cloth_buy, $inputs);

        // insert
        foreach ($inserts as $insert) {
            $result[] = $this->cloth_warehouse_interface->addWarehouse($insert, $user);


            $res = $this->cloth_buy_item_interface->addClothBuyItem($insert, $user, true);
            $result[] = $res['result'];
        }

        // delete
        foreach ($deletes as $delete) {
            $result[] = $this->cloth_buy_item_interface->deleteClothBuyData($delete);
        }

        // update
        foreach ($updates as $update) {
            $result[] = $this->cloth_buy_item_interface->editClothBuyItem($update);

            $params['color_id'] = $update['color_id'];
            $cloth_buy_item = $this->cloth_buy_item_interface->getClothBuyItemById($update);
            if ($cloth_buy_item->metre > $update['metre']) {
                $params['sign'] = 'minus';
                $params['metre'] = $cloth_buy_item->metre - $update['metre'];
            } elseif ($cloth_buy_item->metre < $update['metre']) {
                $params['sign'] = 'plus';
                $params['metre'] = $update['metre'] - $cloth_buy_item->metre;
            } else {
                $params['sign'] = 'equal';
                $params['metre'] = $update['metre'];
            }

            $result[] = $this->cloth_warehouse_interface->editWarehouse($params);
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
        foreach ($cloth_buy->items as $item) {
            $result[] = $this->cloth_buy_item_interface->deleteClothBuyItem($item);
        }
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
