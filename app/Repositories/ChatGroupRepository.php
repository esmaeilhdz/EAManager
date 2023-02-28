<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ChatGroup;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ChatGroupRepository implements Interfaces\iChatGroup
{
    use Common;

    /**
     * لیست دوره های فروش
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getChatGroups($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ChatGroup::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
            ])
                ->select([
                    'id',
                    'name',
                    'is_enable',
                    'created_by',
                    'created_at'
                ])
                ->where('company_id', $company_id)
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getChatGroupById($id, $user, $select = [], $relation = [])
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $chat_group = ChatGroup::where('id', $id)
                ->where('company_id', $company_id);

            if (count($relation)) {
                $chat_group = $chat_group->with($relation);
            }

            if (count($select)) {
                $chat_group = $chat_group->select($select);
            }

            return $chat_group->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editChatGroup($chat_group, $inputs)
    {
        try {
            $chat_group->name = $inputs['name'];
            $chat_group->is_enable = $inputs['is_enable'];

            return $chat_group->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addChatGroup($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $chat_group = new ChatGroup();

            $chat_group->company_id = $company_id;
            $chat_group->name = $inputs['name'];
            $chat_group->created_by = $user->id;

            $result = $chat_group->save();

            return [
                'result' => $result,
                'data' => $result ? $chat_group->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteChatGroup($chat_group)
    {
        try {
            return $chat_group->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
