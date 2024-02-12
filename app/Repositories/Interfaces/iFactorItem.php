<?php

namespace App\Repositories\Interfaces;

interface iFactorItem
{
    public function getById($factor_id, $id, $select = [], $relation = []);

    public function getByFactorId($factor_id, $inputs, $select = [], $relation = []);

    public function editFactorItem($factor_item, $inputs);

    public function addFactorItem(array $inputs, $factor_id, $user);

    public function deleteFactorItems($factor_id);

    public function deleteFactorItem($factor_id, $id);
}
