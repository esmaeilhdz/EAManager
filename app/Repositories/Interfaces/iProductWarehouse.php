<?php

namespace App\Repositories\Interfaces;

interface iProductWarehouse
{
    public function getProductWarehouses($product_id, $inputs, $user);

    public function getProductWarehouseById($inputs, $select = [], $relation = []);

    public function getByProductId($id, $select = [], $relation = []);

    public function editProductWarehouse($product_warehouse, $inputs);

    public function addProductWarehouse($inputs, $user);

    public function deleteProductWarehouse($product);
}
