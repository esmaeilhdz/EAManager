<?php

namespace App\Repositories\Interfaces;

interface iProductPrice
{
    public function getProductPrices($product_id, $inputs);

    public function getProductPriceById($inputs, $select = [], $relation = []);

    public function editProductPrice($product_price, $inputs);

    public function deActiveOldPrices($product_id);

    public function addProductPrice($inputs, $user);

    public function deleteProductPrice($product_price);
}
