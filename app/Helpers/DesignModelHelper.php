<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iDesignModel;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class DesignModelHelper
{
    use Common;

    // attributes
    public iDesignModel $design_model_interface;

    public function __construct(iDesignModel $design_model_interface)
    {
        $this->design_model_interface = $design_model_interface;
    }

    /**
     * لیست طراحی مدل ها
     * @param $inputs
     * @return array
     */
    public function getDesignModels($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'design_models');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $user = Auth::user();
        $design_models = $this->design_model_interface->getDesignModels($inputs, $user);

        $design_models->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'is_confirm' => $item->is_confirm,
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
            'data' => $design_models
        ];
    }

    /**
     * جزئیات طراحی مدل
     * @param $id
     * @return array
     */
    public function getDesignModelDetail($id): array
    {
        $user = Auth::user();
        $select = ['name', 'is_confirm', 'description'];
        $design_model = $this->design_model_interface->getDesignModelById($id, $user, $select);
        if (is_null($design_model)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $design_model
        ];
    }

    /**
     * تایید طراحی مدل
     * @param $inputs
     * @return array
     */
    public function confirmDesignModel($inputs): array
    {
        $user = Auth::user();
        $select = ['id', 'is_confirm'];
        $design_model = $this->design_model_interface->getDesignModelById($inputs['id'], $user, $select);
        if (is_null($design_model)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->design_model_interface->confirmDesignModel($design_model, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * ویرایش طراحی مدل
     * @param $inputs
     * @return array
     */
    public function editDesignModel($inputs): array
    {
        $user = Auth::user();
        $select = ['id', 'name', 'description'];
        $design_model = $this->design_model_interface->getDesignModelById($inputs['id'], $user, $select);
        if (is_null($design_model)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->design_model_interface->editDesignModel($design_model, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * درج طراحی مدل
     * @param $inputs
     * @return array
     */
    public function addDesignModel($inputs): array
    {
        $user = Auth::user();
        $result = $this->design_model_interface->addDesignModel($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * حذف طراحی مدل
     * @param $id
     * @return array
     */
    public function deleteDesignModel($id): array
    {
        $user = Auth::user();
        $select = ['id'];
        $relation = [
            'chat:model_id,model_type'
        ];
        $design_model = $this->design_model_interface->getDesignModelById($id, $user, $select, $relation);
        if (is_null($design_model)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if (count($design_model->chat)) {
            return [
                'result' => false,
                'message' => __('messages.has_chat_cannot_delete'),
                'data' => null
            ];
        }

        $result = $this->design_model_interface->deleteDesignModel($design_model);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
