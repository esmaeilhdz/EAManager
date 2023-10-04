<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iClothSell;
use App\Repositories\Interfaces\iClothSellItems;
use App\Repositories\Interfaces\iClothWarehouse;
use App\Repositories\Interfaces\iCustomer;
use App\Traits\ClothSellTrait;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClothSellHelper
{
    use Common, ClothSellTrait;

    // attributes
    public iClothWarehouse $cloth_warehouse_interface;
    public iClothSell $cloth_sell_interface;
    public iClothSellItems $cloth_sell_item_interface;
    public iCustomer $customer_interface;
    public iCloth $cloth_interface;

    public function __construct(
        iClothWarehouse $cloth_warehouse_interface,
        iClothSell $cloth_sell_interface,
        iClothSellItems $cloth_sell_item_interface,
        iCloth $cloth_interface,
        iCustomer $customer_interface
    )
    {
        $this->cloth_warehouse_interface = $cloth_warehouse_interface;
        $this->cloth_sell_interface = $cloth_sell_interface;
        $this->cloth_sell_item_interface = $cloth_sell_item_interface;
        $this->cloth_interface = $cloth_interface;
        $this->customer_interface = $customer_interface;
    }

    /**
     * لیست فروش پارچه
     * @param $inputs
     * @return array
     */
    public function getClothSells($inputs): array
    {
        $cloth = $this->cloth_interface->getClothByCode($inputs['code'], Auth::user());
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

        $cloth_sells = $this->cloth_sell_interface->getClothSells($inputs);

        $cloth_sells->transform(function ($item) {
            return [
                'id' => $item->id,
                'customer' => $item->customer->name,
                'warehouse_place' => $item->warehouse_place->name,
                'sell_date' => $item->sell_date,
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
            'data' => $cloth_sells
        ];
    }

    /**
     * جزئیات فروش پارچه
     * @param $inputs
     * @return array
     */
    public function getClothSellDetail($inputs): array
    {
        $cloth = $this->cloth_interface->getClothByCode($inputs['code'], Auth::user());
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
     * @throws \App\Exceptions\ApiException
     */
    public function editClothSell($inputs): array
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

        $customer = $this->customer_interface->getCustomerByCode($inputs['customer_code'], $user, ['id']);
        if (is_null($customer)) {
            return [
                'result' => false,
                'message' => __('messages.customer_not_found'),
                'data' => null
            ];
        }

        $inputs['customer_id'] = $customer->id;
        $params['customer_id'] = $customer->id;

        $params = array_merge($params, $inputs);

        $cloth_sell = $this->cloth_sell_interface->getClothSellById($inputs);
        if (is_null($cloth_sell)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $cloth_sell_transaction = $this->getItemsTransaction($cloth_sell->id, $inputs, $user);
        $inserts = $cloth_sell_transaction['insert'];
        $deletes = $cloth_sell_transaction['delete'];
        $updates = $cloth_sell_transaction['update'];

        DB::beginTransaction();

        $result[] = $this->cloth_sell_interface->editClothSell($cloth_sell, $inputs);

        // insert
        foreach ($inserts as $insert) {
            $result[] = $this->cloth_warehouse_interface->addWarehouse($insert, $user);

            $res = $this->cloth_sell_item_interface->addClothSellItem($insert, $user, true);
            $result[] = $res['result'];
        }

        // delete
        foreach ($deletes as $delete) {
            $result[] = $this->cloth_sell_item_interface->deleteClothSellData($delete);
        }

        // update
        foreach ($updates as $update) {
            $params['color_id'] = $update['color_id'];
            $cloth_sell_item = $this->cloth_sell_item_interface->getClothSellItemById($update);
            if ($cloth_sell_item->metre > $update['metre']) {
                $params['sign'] = 'plus';
                $params['metre'] = $cloth_sell_item->metre - $update['metre'];
            } elseif ($cloth_sell_item->metre < $update['metre']) {
                if ($update['metre'] - $cloth_sell_item->metre < 0) {
                    return [
                        'result' => false,
                        'message' => __('messages.not_enough_warehouse_stock'),
                        'data' => null
                    ];
                }
                $params['sign'] = 'minus';
                $params['metre'] = $update['metre'] - $cloth_sell_item->metre;
            } else {
                $params['sign'] = 'equal';
                $params['metre'] = $update['metre'];
            }

            $result[] = $this->cloth_warehouse_interface->editWarehouse($params);
            $result[] = $this->cloth_sell_item_interface->editClothSellItem($update);
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





        /*DB::beginTransaction();
        foreach ($inputs['items'] as $key => $item) {
            $item['cloth_sell_id'] = $cloth_sell->id;
            $params['color_id'] = $item['color_id'];
            $cloth_sell_item = $this->cloth_sell_item_interface->getClothSellItemById($item);
            if (is_null($cloth_sell_item)) {
                return [
                    'result' => false,
                    'message' => __('messages.cloth_item_not_found'),
                    'data' => null
                ];
            }

            if ($cloth_sell_item->metre > $item['metre']) {
                $params['sign'] = 'plus';
                $params['metre'] = $cloth_sell_item->metre - $item['metre'];
            } elseif ($cloth_sell_item->metre < $item['metre']) {
                if ($item['metre'] - $cloth_sell_item->metre < 0) {
                    return [
                        'result' => false,
                        'message' => __('messages.not_enough_warehouse_stock'),
                        'data' => null
                    ];
                }
                $params['sign'] = 'minus';
                $params['metre'] = $item['metre'] - $cloth_sell_item->metre;
            } else {
                $params['sign'] = 'equal';
                $params['metre'] = $item['metre'];
            }

            $result[] = $this->cloth_warehouse_interface->editWarehouse($params);
        }

        $result[] = $this->cloth_sell_interface->editClothSell($cloth_sell, $inputs);
        $result[] = $this->cloth_sell_item_interface->deleteClothSellItems($cloth_sell->id);
        foreach ($inputs['items'] as $key => $item) {
            $item['cloth_sell_id'] = $cloth_sell->id;

            $res = $this->cloth_sell_item_interface->addClothSellItem($item, $user, true);
            $result[] = $res['result'];
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
        ];*/
    }

    /**
     * افزودن فروش پارچه
     * @param $inputs
     * @return array
     */
    public function addClothSell($inputs): array
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

        $customer = $this->customer_interface->getCustomerByCode($inputs['customer_code'], $user, ['id']);
        if (is_null($customer)) {
            return [
                'result' => false,
                'message' => __('messages.customer_not_found'),
                'data' => null
            ];
        }

        DB::beginTransaction();
        $inputs['cloth_id'] = $cloth->id;
        $inputs['customer_id'] = $customer->id;
        $result = [];

        $res_sell = $this->cloth_sell_interface->addClothSell($inputs, $user);
        $cloth_sell_id = $res_sell['data'];
        foreach ($inputs['items'] as $item) {
            $item['cloth_sell_id'] = $cloth_sell_id;
            $cloth_warehouse = $this->cloth_warehouse_interface->getClothWarehousesByCloth($cloth->id, $item['color_id'], $inputs['warehouse_place_id']);
            if (!$cloth_warehouse) {
                return [
                    'result' => false,
                    'message' => __('messages.warehouse_not_exists'),
                    'data' => null
                ];
            }

            if ($cloth_warehouse->metre - $item['metre'] < 0) {
                return [
                    'result' => false,
                    'message' => __('messages.not_enough_warehouse_stock'),
                    'data' => null
                ];
            }

            $res = $this->cloth_sell_item_interface->addClothSellItem($item, $user);
            $result[] = $res['result'];
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
            'data' => $cloth_sell_id ?? null
        ];
    }

    /**
     * حذف فروش پارچه
     * @param $inputs
     * @return array
     */
    public function deleteClothSell($inputs): array
    {
        $cloth = $this->cloth_interface->getClothByCode($inputs['code'], Auth::user());
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

        DB::beginTransaction();
        foreach ($cloth_sell->items as $item) {
            $result[] = $this->cloth_sell_item_interface->deleteClothSellItem($item);
        }
        $result[] = $this->cloth_sell_interface->deleteClothSell($cloth_sell);

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
