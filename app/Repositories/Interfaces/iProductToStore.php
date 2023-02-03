<?php

namespace App\Repositories\Interfaces;

interface iProductToStore
{
    public function getProductToStores($product_warehouse, $inputs);

    public function getProductToStoreById($product_warehouse_id, $id, $select = []);

    public function editProductToStore($product_to_store, $inputs);

    public function addProductToStore($inputs, $user, $quiet = false);

    public function deleteProductToStore($product_to_store);
}
