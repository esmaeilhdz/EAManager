<?php

namespace App\Repositories\Interfaces;

interface iRequestProductWarehouse
{
    public function getRequestProductWarehouses($inputs);

    public function getRequestProductWarehouseById($inputs, $select = [], $relation = []);

    public function editRequestProductWarehouse($request_product_warehouse, $inputs);

    public function confirmRequestProductWarehouse($request_product_warehouse, $user);

    public function addRequestProductWarehouse($inputs, $user);

    public function deleteRequestProductWarehouse($request_product_warehouse);
}
