<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Chat;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ChatRepository implements Interfaces\iChat
{
    use Common;

    /**
     * لیست دوره های فروش
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getChats($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Chat::with([
                'chat_group_person:id,person_id',
                'chat_group_person.person' => function ($q) {
                    $q->select(['id', 'code', 'name', 'family']);
                }
            ])
                ->select([
                    'id',
                    'chat_group_person_id',
                    'content',
                    'created_at'
                ])
                ->where('model_type', $inputs['model_type'])
                ->where('model_id', $inputs['model_id'])
                ->whereHas('chat_group_person.chat_group', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->whereHas('chat_group_person.person', function ($q) use ($inputs) {
                    $q->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params']);
                })
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getChatById($inputs, $user, $select = [], $relation = [])
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $chat = Chat::where('id', $inputs['id'])
                ->where('model_type', $inputs['model_type'])
                ->where('model_id', $inputs['model_id'])
                ->whereHas('chat_group_person.chat_group', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                });

            if (count($select)) {
                $chat = $chat->select($select);
            }

            if (count($relation)) {
                $chat = $chat->with($relation);
            }

            return $chat->first();

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

}
