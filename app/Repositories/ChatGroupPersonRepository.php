<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ChatGroupPerson;
use App\Traits\Common;
use Illuminate\Database\Eloquent\Collection;

class ChatGroupPersonRepository implements Interfaces\iChatGroupPerson
{
    use Common;

    /**
     * لیست افراد گروه چت
     * @param $inputs
     * @return Collection|array
     * @throws ApiException
     */
    public function getChatGroupPersons($inputs): Collection|array
    {
        try {
            return ChatGroupPerson::with([
                'person:id,code,name,family',
                'creator:id,person_id',
                'creator.person:id,name,family',
            ])
                ->select([
                    'id',
                    'person_id',
                    'created_by',
                    'created_at'
                ])
                ->where('chat_group_id', $inputs['chat_group_id'])
                ->orderByRaw($inputs['order_by'])
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getChatGroupPersonById($inputs, $select = [], $relation = [])
    {
        try {
            $chat_group_person = ChatGroupPerson::where('chat_group_id', $inputs['chat_group_id'])
                ->where('id', $inputs['id']);

            if (count($relation)) {
                $chat_group_person = $chat_group_person->with($relation);
            }

            if (count($select)) {
                $chat_group_person = $chat_group_person->select($select);
            }

            return $chat_group_person->first();

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editChatGroupPerson($inputs)
    {
        try {
            return ChatGroupPerson::where('id', $inputs['id'])
                ->update([
                    'name' => $inputs['name'],
                    'start_date' => $inputs['start_date'],
                    'end_date' => $inputs['end_date']
                ]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addChatGroupPerson($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $chat_group_person = new ChatGroupPerson();

            $chat_group_person->company_id = $company_id;
            $chat_group_person->name = $inputs['name'];
            $chat_group_person->start_date = $inputs['start_date'];
            $chat_group_person->end_date = $inputs['end_date'];
            $chat_group_person->created_by = $user->id;

            $result = $chat_group_person->save();

            return [
                'result' => $result,
                'data' => $result ? $chat_group_person->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteChatGroupPerson($id)
    {
        try {
            return ChatGroupPerson::where('id', $id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
