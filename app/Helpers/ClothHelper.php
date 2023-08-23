<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iClothBuy;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClothHelper
{
    use Common;

    // attributes
    public iCloth $cloth_interface;
    public iClothBuy $cloth_buy_interface;

    public function __construct(iCloth $cloth_interface, iClothBuy $cloth_buy_interface)
    {
        $this->cloth_interface = $cloth_interface;
        $this->cloth_buy_interface = $cloth_buy_interface;
    }

    /**
     * لیست پارچه ها
     * @param $inputs
     * @return array
     */
    public function getClothes($inputs): array
    {
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $clothes = $this->cloth_interface->getClothes($inputs);

        $clothes->transform(function ($item) {
            return [
                'code' => $item->code,
                'name' => $item->name,
                'color' => [
                    'id' => $item->color_id,
                    'caption' => $item->color->enum_caption
                ],
                'cloth_buy_count' => count($item->cloth_buy),
                'cloth_sell_count' => count($item->cloth_sell),
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
        $cloth = $this->cloth_interface->getClothByCode($code);
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
     * ویرایش پارچه
     * @param $inputs
     * @return array
     */
    public function editCloth($inputs): array
    {
        $cloth = $this->cloth_interface->getClothByCode($inputs['code']);
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
     * @throws \App\Exceptions\ApiException
     */
    public function addCloth($inputs): array
    {
        $user = Auth::user();
        $company_id = $this->getCurrentCompanyOfUser($user);

        DB::beginTransaction();
        $result = [];
        foreach ($inputs['color_id'] as $color_id) {
            $inputs['color_id_item'] = $color_id;
            $res = $this->cloth_interface->addCloth($inputs, $user, $company_id);
            $result[] = $res['result'];
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
     * حذف پارچه
     * @param $code
     * @return array
     */
    public function deleteCloth($code): array
    {
        $cloth = $this->cloth_interface->getClothByCode($code);
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
