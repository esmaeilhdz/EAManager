<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Repositories\Interfaces\iChatGroup;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class ChatGroupHelper
{
    use Common;

    // attributes
    public iChatGroup $chat_group_interface;

    public function __construct(iChatGroup $chat_group_interface)
    {
        $this->chat_group_interface = $chat_group_interface;
    }

    /**
     * لیست گروه های چت
     * @param $inputs
     * @return array
     */
    public function getChatGroups($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $user = Auth::user();
        $inputs['order_by'] = $this->orderBy($inputs, 'chat_groups');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $chat_groups = $this->chat_group_interface->getChatGroups($inputs, $user);

        $chat_groups->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'is_enable' => $item->is_enable,
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
            'data' => $chat_groups
        ];
    }

    /**
     * جزئیات گروه چت
     * @param $id
     * @return array
     */
    public function getChatGroupDetail($id): array
    {
        $user = Auth::user();
        $select = ['id', 'name', 'is_enable'];
        $relation = [
            'chat_group_persons:chat_group_id'
        ];
        $chat_group = $this->chat_group_interface->getChatGroupById($id, $user, $select, $relation);
        if (is_null($chat_group)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $return = [
            'id' => $chat_group->id,
            'name' => $chat_group->name,
            'is_enable' => $chat_group->is_enable,
            'chat_group_person_count' => count($chat_group->chat_group_persons)
        ];

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $return
        ];
    }

    /**
     * ویرایش گروه چت
     * @param $inputs
     * @return array
     */
    public function editChatGroup($inputs): array
    {
        $user = Auth::user();
        $chat_group = $this->chat_group_interface->getChatGroupById($inputs['id'], $user);
        if (is_null($chat_group)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->chat_group_interface->editChatGroup($chat_group, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن گروه چت
     * @param $inputs
     * @return array
     */
    public function addChatGroup($inputs): array
    {
        $user = Auth::user();
        $result = $this->chat_group_interface->addChatGroup($inputs, $user);
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
    public function deleteChatGroup($id): array
    {
        $user = Auth::user();
        $relation = [
            'chat_group_persons:chat_group_id'
        ];
        $chat_group = $this->chat_group_interface->getChatGroupById($id, $user, ['id'], $relation);
        if (is_null($chat_group)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if (count($chat_group->chat_group_persons)) {
            return [
                'result' => false,
                'message' => __('messages.group_chat_has_member_delete_error'),
                'data' => null
            ];
        }

        $result = $this->chat_group_interface->deleteChatGroup($chat_group);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
