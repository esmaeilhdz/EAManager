<?php

namespace App\Repositories\Interfaces;

interface iAccessory
{
    public function getAccessories($inputs, $user);

    public function getAccessoryById($id, $select = [], $relation = []);

    public function getAccessoryCombo($inputs, $user);

    public function editAccessory($inputs);

    public function changeStatusAccessory($accessory, $inputs);

    public function addAccessory($inputs, $user);

    public function deleteAccessory($id);
}
