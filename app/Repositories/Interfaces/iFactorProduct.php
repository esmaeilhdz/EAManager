<?php

namespace App\Repositories\Interfaces;

interface iFactorProduct
{
    public function getById($factor_id, $id, $select = [], $relation = []);

    public function getByFactorId($factor_id, $inputs, $select = [], $relation = []);

    public function editFactorProduct($factor_product, $inputs);

    public function addFactorProduct(array $inputs, $factor_id, $user);

    public function deleteFactorProducts($factor_id);

    public function deleteFactorProduct($factor_id, $id);
}
