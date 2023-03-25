<?php

namespace App\Repositories\Interfaces;

interface iRole
{
    public function getRoles($inputs, $user);

    public function getRoleByCode($code, $select = [], $relation = []);

    public function editRole($role, $inputs);

    public function addRole($inputs, $user);

    public function deleteRole($role);
}
