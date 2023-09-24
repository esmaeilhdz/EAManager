<?php

namespace App\Repositories\Interfaces;

interface iProductAccessory
{
    public function getProductAccessories($inputs, $user);

    public function getById($product_id, $id, $user, $relation = []);

    public function getCombo($user);

    public function editProductAccessory($product_accessory, $inputs);

    public function addProductAccessory($inputs, $user);

    public function deleteProductAccessories($product_id);

    public function deleteProductAccessory($product_accessory);
}
