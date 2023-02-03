<?php

namespace App\Repositories\Interfaces;

interface iAccessoryWarehouse
{
    public function getAccessoryWarehouses($inputs);

    public function getAccessoryWarehouseById($id);

    public function editAccessoryWarehouse($inputs);

    public function addAccessoryWarehouse($inputs, $user);

    public function deleteAccessoryWarehouse($id);
}
