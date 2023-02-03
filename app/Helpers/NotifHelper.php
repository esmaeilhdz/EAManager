<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iNotif;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class NotifHelper
{
    use Common;

    // attributes
    public iNotif $notif_interface;

    public function __construct(iNotif $notif_interface)
    {
        $this->notif_interface = $notif_interface;
    }

    /**
     * لیست اعلان ها
     * @param $inputs
     * @return array
     */
    public function getNotifs($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;family;national_code;concat_ws(" ",name,family);replace(concat_ws("",name,family)," ","")');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'notifs');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $notifs = $this->notif_interface->getNotifs($inputs);

        $notifs->transform(function ($item) {
            return [
                'code' => $item->code,
                'subject' => $item->subject,
                'sender_user' => [
                    'full_name' => $item->sender_user->person->name . ' ' . $item->sender_user->person->family
                ],
                'receiver_user' => [
                    'full_name' => $item->receiver_user->person->name . ' ' . $item->receiver_user->person->family
                ],
                'notification_level' => [
                    'id' => $item->notification_level_id,
                    'caption' => $item->notification_level->enum_caption
                ],
                'receiver_is_read' => $item->receiver_is_read,
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
            'data' => $notifs
        ];
    }

    /**
     * جزئیات اعلان
     * @param $code
     * @return array
     */
    public function getNotifDetail($code): array
    {
        $notif = $this->notif_interface->getNotifByCode($code);
        if (is_null($notif)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $user = Auth::user();
        if ($user->id == $notif->receiver_user_id) {
            $this->notif_interface->setReadNotif($notif->id);
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $notif
        ];
    }

    /**
     * ویرایش اعلان
     * @param $inputs
     * @return array
     */
    public function editNotif($inputs): array
    {
        $notif = $this->notif_interface->getNotifByCode($inputs['code']);
        if (is_null($notif)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $user = Auth::user();
        if ($user->id != $notif->created_by) {
            return [
                'result' => false,
                'message' => __('messages.not_allowed'),
                'data' => null
            ];
        }

        $result = $this->notif_interface->editNotif($notif, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن اعلان
     * @param $inputs
     * @return array
     */
    public function addNotif($inputs): array
    {
        $user = Auth::user();
        $result = $this->notif_interface->addNotif($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * حذف اعلان
     * @param $code
     * @return array
     */
    public function deleteNotif($code): array
    {
        $notif = $this->notif_interface->getNotifByCode($code);
        if (is_null($notif)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->notif_interface->deleteNotif($notif);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
