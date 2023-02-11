<?php

namespace App\Repositories\Interfaces;

interface iFactor
{
    public function getFactors($inputs);

    public function getFactorByCode($code, $select = [], $relation = []);

    public function editFactor($factor, $inputs);

    public function addFactor($inputs, $user);

    public function deleteFactor($factor);
}
