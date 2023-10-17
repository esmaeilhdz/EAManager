<?php

namespace App\Repositories\Interfaces;

interface iWarehouseItem
{
    public function getWarehouseItems($inputs, $user);

    public function getWarehouseItemById($id, $user, $select = [], $relation = []);

    public function editWarehouseItem($warehouse_item, $inputs, $user);

    public function addWarehouseItem($inputs);

    public function deleteWarehouseItem($warehouse_item);
}
