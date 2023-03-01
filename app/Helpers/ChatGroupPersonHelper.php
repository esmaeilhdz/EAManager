<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Repositories\Interfaces\iChatGroup;
use App\Repositories\Interfaces\iChatGroupPerson;
use App\Repositories\Interfaces\iPerson;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class ChatGroupPersonHelper
{
    use Common;

    // attributes
    public iPerson $person_interface;
    public iChatGroup $chat_group_interface;
    public iChatGroupPerson $chat_group_person_interface;

    public function __construct(
        iPerson $person_interface,
        iChatGroup $chat_group_interface,
        iChatGroupPerson $chat_group_person_interface
    )
    {
        $this->person_interface = $person_interface;
        $this->chat_group_interface = $chat_group_interface;
        $this->chat_group_person_interface = $chat_group_person_interface;
    }

    /**
     * لیست افراد گروه چت
     * @param $inputs
     * @return array
     */
    public function getChatGroupPersons($inputs): array
    {
        $user = Auth::user();
        $chat_group = $this->chat_group_interface->getChatGroupById($inputs['chat_group_id'], $user, ['name']);
        if (is_null($chat_group)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['order_by'] = $this->orderBy($inputs, 'chat_group_persons');

        $chat_group_persons = $this->chat_group_person_interface->getChatGroupPersons($inputs);

        $chat_group_persons->transform(function ($item) {
            return [
                'id' => $item->id,
                'person' => [
                    'code' => $item->person->code,
                    'name' => $item->person->name,
                    'family' => $item->person->family,
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
            'data' => $chat_group_persons,
            'other' => [
                'chat_group_name' => $chat_group->name
            ]
        ];
    }

    /**
     * جزئیات فرد گروه چت
     * @param $inputs
     * @return array
     */
    public function getChatGroupPersonDetail($inputs): array
    {
        $user = Auth::user();
        $chat_group = $this->chat_group_interface->getChatGroupById($inputs['chat_group_id'], $user, ['name']);
        if (is_null($chat_group)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $select = ['id', 'person_id'];
        $relation = [
            'chat:model_type,model_id,chat_group_person_id,content',
            'chat.model',
            'person:id,code,name,family'
        ];
        $chat_group_person = $this->chat_group_person_interface->getChatGroupPersonById($inputs, $select, $relation);
        if (is_null($chat_group_person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        foreach ($chat_group_person->chat as $chat) {
            $model_name = $chat->model->name;
            unset($chat->model);
            switch ($chat->model_type) {
                case is_numeric(strpos($chat->model_type, 'Design')):
                    $model_caption = 'طراحی مدل';
                break;
                case is_numeric(strpos($chat->model_type, 'Product')):
                    $model_caption = 'کالا';
                break;
                default:
                    $model_caption = null;
            }

            $chat['model_caption'] = $model_caption;
            $chat['model_name'] = $model_name;
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $chat_group_person
        ];
    }

    /**
     * افزودن فرد گروه چت
     * @param $inputs
     * @return array
     */
    public function addChatGroupPerson($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['person_code'], ['id']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['person_id'] = $person->id;
        $user = Auth::user();
        $chat_group = $this->chat_group_interface->getChatGroupById($inputs['chat_group_id'], $user, ['name']);
        if (is_null($chat_group)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->chat_group_person_interface->addChatGroupPerson($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * حذف فرد از گروه چت
     * @param $id
     * @return array
     */
    public function deleteChatGroupPerson($inputs): array
    {
        $user = Auth::user();
        $chat_group = $this->chat_group_interface->getChatGroupById($inputs['chat_group_id'], $user, ['name']);
        if (is_null($chat_group)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $chat_group_person = $this->chat_group_person_interface->getChatGroupPersonById($inputs);
        if (is_null($chat_group_person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->chat_group_person_interface->deleteChatGroupPerson($chat_group_person);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
