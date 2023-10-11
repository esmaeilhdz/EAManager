<?php

namespace App\Repositories\Interfaces;

interface iProductAccessoryPrice
{
    public function getById($id);

    public function getByIds($product_accessory_id, $product_price_id);

    public function addProductAccessoryPrice($inputs, $product_price_id);

    public function editProductAccessoryPrice($product_accessory_price, $inputs);

    public function deleteByProductPriceId($product_price_id);

    public function deleteByProductAccessoryId($product_accessory_id);
}
