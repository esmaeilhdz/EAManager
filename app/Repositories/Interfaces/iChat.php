<?php

namespace App\Repositories\Interfaces;

interface iChat
{
    public function getChats($inputs, $user);

    public function getChatById($inputs, $user, $select = [], $relation = []);

}
