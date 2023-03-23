<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Repositories\Interfaces\iGroupConversation;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class GroupConversationHelper
{
    use Common;

    // attributes
    public iGroupConversation $group_conversation_interface;

    public function __construct(iGroupConversation $group_conversation_interface)
    {
        $this->group_conversation_interface = $group_conversation_interface;
    }

    /**
     * لیست گروه های چت
     * @param $inputs
     * @return array
     */
    public function getGroupConversations($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $user = Auth::user();
        $inputs['order_by'] = $this->orderBy($inputs, 'chat_groups');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $chat_groups = $this->group_conversation_interface->getGroupConversations($inputs, $user);

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
    public function getGroupConversationDetail($id): array
    {
        $user = Auth::user();
        $select = ['id', 'name', 'is_enable'];
        $relation = [
            'chat_group_persons:chat_group_id'
        ];
        $chat_group = $this->group_conversation_interface->getChatGroupById($id, $user, $select, $relation);
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
    public function editGroupConversation($inputs): array
    {
        $user = Auth::user();
        $chat_group = $this->group_conversation_interface->getChatGroupById($inputs['id'], $user);
        if (is_null($chat_group)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->group_conversation_interface->editChatGroup($chat_group, $inputs);
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
    public function addGroupConversation($inputs): array
    {
        $user = Auth::user();
        $result = $this->group_conversation_interface->addChatGroup($inputs, $user);
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
    public function deleteGroupConversation($id): array
    {
        $user = Auth::user();
        $relation = [
            'chat_group_persons:chat_group_id'
        ];
        $chat_group = $this->group_conversation_interface->getChatGroupById($id, $user, ['id'], $relation);
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

        $result = $this->group_conversation_interface->deleteChatGroup($chat_group);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
