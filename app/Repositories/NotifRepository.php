<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Notif;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotifRepository implements Interfaces\iNotif
{
    use Common;

    /**
     * لیست اعلان ها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getNotifs($inputs): LengthAwarePaginator
    {
        try {
            return Notif::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'sender_user:id,person_id',
                'sender_user.person:id,name,family',
                'receiver_user:id,person_id',
                'receiver_user.person:id,name,family',
                'notification_level:enum_id,enum_caption',
            ])
                ->select([
                    'code',
                    'subject',
                    'sender_user_id',
                    'receiver_user_id',
                    'notification_level_id',
                    'created_by',
                    'created_at'
                ])
                ->where(function ($q) use ($inputs) {
                    $q->whereHas('sender_user.person', function ($q2) use ($inputs) {
                        $q2->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params']);
                    })
                    ->orWhereHas('receiver_user.person', function ($q2) use ($inputs) {
                        $q2->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params']);
                    });
                })
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات اعلان
     * @param $code
     * @return mixed
     * @throws ApiException
     */
    public function getNotifByCode($code): mixed
    {
        try {
            return Notif::with([
                'sender_user:id,person_id',
                'sender_user.person:id,name,family',
                'receiver_user:id,person_id',
                'receiver_user.person:id,name,family',
                'notification_level:enum_id,enum_caption',
            ])
                ->select([
                    'id',
                    'subject',
                    'sender_user_id',
                    'receiver_user_id',
                    'notification_level_id',
                    'created_by',
                    'description'
                ])
                ->whereCode($code)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش اعلان
     * @param $notif
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editNotif($notif, $inputs): mixed
    {
        try {
            $notif->subject = $inputs['subject'];
            $notif->description = $inputs['description'];
            $notif->receiver_user_id = $inputs['receiver_user_id'];
            $notif->notification_level_id = $inputs['notification_level_id'];

            return $notif->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function setReadNotif($id)
    {
        try {
            Notif::where('id', $id)->update(['receiver_is_read' => 1]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن اعلان
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addNotif($inputs, $user): array
    {
        try {
            $notif = new Notif();

            $notif->code = $this->randomString();
            $notif->subject = $inputs['subject'];
            $notif->description = $inputs['description'];
            $notif->sender_user_id = $user->id;
            $notif->receiver_user_id = $inputs['receiver_user_id'];
            $notif->notification_level_id = $inputs['notification_level_id'];
            $notif->created_by = $user->id;

            $result = $notif->save();

            return [
                'result' => $result,
                'data' => $result ? $notif->code : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف اعلان
     * @param $notif
     * @return mixed
     * @throws ApiException
     */
    public function deleteNotif($notif): mixed
    {
        try {
            return $notif->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
