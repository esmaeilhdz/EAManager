<?php

namespace App\Repositories\Interfaces;

interface iProduct
{
    public function getProducts($inputs);

    public function getProductByCode($code, $select = []);

    public function editProduct($product, $inputs);

    public function addProduct($inputs, $user);

    public function deleteProduct($product);
}
