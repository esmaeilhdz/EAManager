<?php

namespace App\Repositories\Interfaces;

interface iProductModel
{
    public function getProductModels($inputs, $user);

    public function getById($product_id, $id, $user, $relation = []);

    public function getProductsModelCombo($inputs, $user);

    public function getProductModelCombo($inputs, $user);

    public function editProductModel($product_model, $inputs);

    public function addProductModel($inputs, $user);

    public function deleteProductModel($product_model);
}
