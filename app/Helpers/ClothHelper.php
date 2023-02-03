<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCloth;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class ClothHelper
{
    use Common;

    // attributes
    public iCloth $cloth_interface;

    public function __construct(iCloth $cloth_interface)
    {
        $this->cloth_interface = $cloth_interface;
    }

    /**
     * لیست پارچه ها
     * @param $inputs
     * @return array
     */
    public function getClothes($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

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
        $place = $this->cloth_interface->getClothByCode($inputs['code']);
        if (is_null($place)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->cloth_interface->editCloth($inputs);
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
        $result = $this->cloth_interface->addCloth($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
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
