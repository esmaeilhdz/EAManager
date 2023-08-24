<?php

namespace App\Repositories\Interfaces;

interface iProductWarehouse
{
    public function getProductWarehouses($product_id, $inputs, $user);

    public function getProductWarehouseById($inputs, $user, $select = [], $relation = []);

    public function getDestinationProductWarehouseById($inputs, $select = [], $relation = []);

    public function getByProductId($id, $select = [], $relation = []);

    public function getById($id, $select = [], $relation = []);

    public function getByStockProduct($inputs, $data, $select = [], $relation = []);

    public function getByPlaceId($inputs, $user);

    public function getByProductAndPlace($place_id, $product_id, $user);

    public function getProductWarehouseCombo($inputs, $user);

    public function editProductWarehouse($product_warehouse, $inputs);

    public function deActiveOldWarehouses($inputs);

    public function addProductWarehouse($inputs, $user);

    public function deleteProductWarehouse($product_warehouse);
}
