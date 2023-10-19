<?php

namespace App\Repositories\Interfaces;

interface iWarehouse
{
    public function getWarehouses($inputs, $user);

    public function getWarehouseByCode($code, $user, $select = [], $relation = []);

    public function getWarehousesCombo($inputs, $user);

    public function editWarehouse($inputs, $user);

    public function addWarehouse($inputs, $user);

    public function deleteWarehouse($warehouse);
}
