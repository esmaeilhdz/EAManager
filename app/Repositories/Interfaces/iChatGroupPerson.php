<?php

namespace App\Repositories\Interfaces;

interface iChatGroupPerson
{
    public function getChatGroupPersons($inputs);

    public function getChatGroupPersonById($inputs, $select = [], $relation = []);

    public function addChatGroupPerson($inputs, $user);

    public function deleteChatGroupPerson($id);
}
