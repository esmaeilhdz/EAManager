<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPlace;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class PlaceHelper
{
    use Common;

    // attributes
    public iPlace $place_interface;

    public function __construct(iPlace $place_interface)
    {
        $this->place_interface = $place_interface;
    }

    /**
     * لیست مکان ها
     * @param $inputs
     * @return array
     */
    public function getPlaces($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $places = $this->place_interface->getPlaces($inputs);

        $places->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'place_kind' => [
                    'id' => $item->place_kind_id,
                    'caption' => $item->place_kind->enum_caption,
                ],
                'department_manager_name' => $item->department_manager_name,
                'department_manager_national_code' => $item->department_manager_national_code,
                'department_manager_identity' => $item->department_manager_identity,
                'capacity' => $item->capacity,
                'from_date' => $item->from_date,
                'wheel_machine_count' => !count($item->place_attribute) ? null : [
                    'caption' => $item->place_attribute[0]->attribute->enum_caption,
                    'value' => $item->place_attribute[0]->value
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
            'data' => $places
        ];
    }

    /**
     * جزئیات مکان
     * @param $id
     * @return array
     */
    public function getPlaceDetail($id): array
    {
        $place = $this->place_interface->getPlaceById($id);
        if (is_null($place)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $place
        ];
    }

    /**
     * کامبوی مکان
     * @param $inputs
     * @return array
     */
    public function getPlaceCombo($inputs): array
    {
        $user = Auth::user();
        $places = $this->place_interface->getPlaceCombo($inputs, $user);

        $places->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $places
        ];
    }

    /**
     * ویرایش مکان
     * @param $inputs
     * @return array
     */
    public function editPlace($inputs): array
    {
        $place = $this->place_interface->getPlaceById($inputs['id']);
        if (is_null($place)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->place_interface->editPlace($inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    public function addPlace($inputs): array
    {
        $user = Auth::user();
        $result = $this->place_interface->addPlace($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function deletePlace($id): array
    {
        $place = $this->place_interface->getPlaceById($id);
        if (is_null($place)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->place_interface->deletePlace($id);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
