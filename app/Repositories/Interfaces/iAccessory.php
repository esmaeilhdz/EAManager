<?php

namespace App\Repositories\Interfaces;

interface iAccessory
{
    public function getAccessories($inputs);

    public function getAccessoryById($id);

    public function editAccessory($inputs);

    public function addAccessory($inputs, $user);

    public function deleteAccessory($id);
}
