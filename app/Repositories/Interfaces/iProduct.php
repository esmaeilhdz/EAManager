<?php

namespace App\Repositories\Interfaces;

interface iProduct
{
    public function getProducts($inputs);

    public function getProductByCode($code, $user, $select = []);

    public function getProductsCombo($inputs, $user);

    public function editProduct($product, $inputs);

    public function addProduct($inputs, $user);

    public function deleteProduct($product);
}
