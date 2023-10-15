<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iClothBuy;
use App\Repositories\Interfaces\iClothBuyItems;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClothHelper
{
    use Common;

    // attributes
    public iCloth $cloth_interface;
    public iClothBuy $cloth_buy_interface;
    public iClothBuyItems $cloth_buy_item_interface;

    public function __construct(
        iCloth $cloth_interface,
        iClothBuy $cloth_buy_interface,
        iClothBuyItems $cloth_buy_item_interface,
    )
    {
        $this->cloth_interface = $cloth_interface;
        $this->cloth_buy_interface = $cloth_buy_interface;
        $this->cloth_buy_item_interface = $cloth_buy_item_interface;
    }

    /**
     * لیست پارچه ها
     * @param $inputs
     * @return array
     */
    public function getClothes($inputs): array
    {
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $clothes = $this->cloth_interface->getClothes($inputs, Auth::user());

        $clothes->transform(function ($item) {
            $cloth_buys = null;
            foreach ($item->cloth_buy as $cloth_buy) {
                $cloth_buys[] = [
                    'seller_place' => $cloth_buy->seller_place->name,
                    'warehouse_place' => $cloth_buy->warehouse_place->name,
                    'receive_date' => $cloth_buy->receive_date,
                    'factor_no' => $cloth_buy->factor_no,
                    'price' => $cloth_buy->price,
                ];
            }

            $cloth_sells = null;
            foreach ($item->cloth_sell as $cloth_sell) {
                $cloth_sells[] = [
                    'customer' => $cloth_sell->customer->name,
                    'warehouse_place' => $cloth_sell->warehouse_place->name,
                    'sell_date' => $cloth_sell->sell_date,
                    'factor_no' => $cloth_sell->factor_no,
                    'price' => $cloth_sell->price,
                ];
            }
            return [
                'code' => $item->code,
                'name' => $item->name,
                'cloth_buys' => $cloth_buys,
                'cloth_sells' => $cloth_sells,
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
     * جزئیات پارچه
     * @param $code
     * @return array
     */
    public function getClothDetail($code): array
    {
        $cloth = $this->cloth_interface->getClothByCode($code, Auth::user());
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $cloth
        ];
    }

    /**
     * کامبوی پارچه
     * @param $inputs
     * @return array
     */
    public function getClothCombo($inputs): array
    {
        $cloths = $this->cloth_interface->getClothCombo($inputs, Auth::user());

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $cloths
        ];
    }

    /**
     * ویرایش پارچه
     * @param $inputs
     * @return array
     */
    public function editCloth($inputs): array
    {
        $cloth = $this->cloth_interface->getClothByCode($inputs['code'], Auth::user());
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->cloth_interface->editCloth($cloth, $inputs);

        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن پارچه
     * @param $inputs
     * @return array
     */
    public function addCloth($inputs): array
    {
        $user = Auth::user();

        DB::beginTransaction();
        $result = [];
        $res = $this->cloth_interface->addCloth($inputs, $user);
        $result[] = $res['result'];
        $inputs['cloth_id'] = $res['data']->id;
        $cloth_code = $res['data']->code ?? null;
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
            'data' => $cloth_code
        ];
    }

    /**
     * حذف پارچه
     * @param $code
     * @return array
     */
    public function deleteCloth($code): array
    {
        $cloth = $this->cloth_interface->getClothByCode($code, Auth::user());
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->cloth_interface->deleteCloth($code);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
