<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iAccessory;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class AccessoryHelper
{
    use Common;

    // attributes
    public iAccessory $accessory_interface;

    public function __construct(iAccessory $accessory_interface)
    {
        $this->accessory_interface = $accessory_interface;
    }

    /**
     * لیست خرج کار ها
     * @param $inputs
     * @return array
     */
    public function getAccessories($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $accessories = $this->accessory_interface->getAccessories($inputs);

        $accessories->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'is_enable' => $item->is_enable,
                'warehouse_count' => $item->warehouse->count ?? null,
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
            'data' => $accessories
        ];
    }

    /**
     * جزئیات خرج کار
     * @param $id
     * @return array
     */
    public function getAccessoryDetail($id): array
    {
        $select = ['id', 'name', 'is_enable'];
        $relation = [
            'warehouse:accessory_id,count',
            'accessoryBuys:accessory_id,place_id,count,created_at',
            'accessoryBuys.place:id,name'
        ];
        $accessory = $this->accessory_interface->getAccessoryById($id, $select, $relation);
        if (is_null($accessory)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $accessory
        ];
    }

    /**
     * ویرایش خرج کار
     * @param $inputs
     * @return array
     */
    public function editAccessory($inputs): array
    {
        $accessory = $this->accessory_interface->getAccessoryById($inputs['id']);
        if (is_null($accessory)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->accessory_interface->editAccessory($inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * ویرایش وضعیت خرج کار
     * @param $inputs
     * @return array
     */
    public function changeStatusAccessory($inputs): array
    {
        $select = ['id', 'is_enable'];
        $accessory = $this->accessory_interface->getAccessoryById($inputs['id'], $select);
        if (is_null($accessory)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->accessory_interface->changeStatusAccessory($accessory, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن خرج کار
     * @param $inputs
     * @return array
     */
    public function addAccessory($inputs): array
    {
        $user = Auth::user();
        $result = $this->accessory_interface->addAccessory($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * حذف خرج کار
     * @param $id
     * @return array
     */
    public function deleteAccessory($id): array
    {
        $account = $this->accessory_interface->getAccessoryById($id);
        if (is_null($account)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->accessory_interface->deleteAccessory($id);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
