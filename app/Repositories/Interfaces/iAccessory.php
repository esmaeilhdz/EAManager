<?php

namespace App\Repositories\Interfaces;

interface iAccessory
{
    public function getAccessories($inputs);

    public function getAccessoryById($id, $select = [], $relation = []);

    public function editAccessory($inputs);

    public function changeStatusAccessory($accessory, $inputs);

    public function addAccessory($inputs, $user);

    public function deleteAccessory($id);
}
