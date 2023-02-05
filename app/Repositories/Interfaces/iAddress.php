<?php

namespace App\Repositories\Interfaces;

interface iAddress
{
    public function getAddressById($inputs, $select = [], $relation = []);

    public function editAddress($address, $inputs);

    public function addAddress($inputs, $user);

    public function deleteAddress($address);
}
