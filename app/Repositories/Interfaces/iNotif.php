<?php

namespace App\Repositories\Interfaces;

interface iNotif
{
    public function getNotifs($inputs);

    public function getNotifByCode($code);

    public function editNotif($notif, $inputs);

    public function setReadNotif($id);

    public function addNotif($inputs, $user);

    public function deleteNotif($notif);
}
