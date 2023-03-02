<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Repositories\Interfaces\iChat;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatHelper
{
    use Common;

    // attributes
    public iChat $chat_interface;

    public function __construct(iChat $chat_interface)
    {
        $this->chat_interface = $chat_interface;
    }

    /**
     * لیست چت ها
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function getChats($inputs): array
    {
        $user = Auth::user();
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;family;national_code;concat_ws(" ",name,family);replace(concat_ws("",name,family)," ","")');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        $inputs['order_by'] = $this->orderBy($inputs, 'chats');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $chats = $this->chat_interface->getChats($inputs, $user);

        $chats->transform(function ($item) {
            return [
                'id' => $item->id,
                'person' => [
                    'name' => $item->chat_group_person->person->name,
                    'family' => $item->chat_group_person->person->family
                ],
                'content' => mb_substr($item->content, 0, 30),
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $chats
        ];
    }

    /**
     * جزئیات چت
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function getChatDetail($inputs): array
    {
        $user = Auth::user();

        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        $chat = $this->chat_interface->getChatById($inputs, $user, ['content']);
        if (is_null($chat)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $chat
        ];
    }

}
