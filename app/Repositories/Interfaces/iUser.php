<?php

namespace App\Repositories\Interfaces;

interface iUser
{
    public function getUsers($inputs, $user);

    public function getUserByCode($code, $select = []);

    public function editUser($inputs, $user);

    public function addUser($inputs, $user_login);

    public function deleteUser($user);
}
