<?php

namespace App\Repositories\Interfaces;

interface iChatGroup
{
    public function getChatGroups($inputs, $user);

    public function getChatGroupById($id, $user, $select = [], $relation = []);

    public function editChatGroup($chat_group, $inputs);

    public function addChatGroup($inputs, $user);

    public function deleteChatGroup($chat_group);
}
