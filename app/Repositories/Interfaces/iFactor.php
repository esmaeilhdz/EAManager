<?php

namespace App\Repositories\Interfaces;

interface iFactor
{
    public function getFactors($inputs);

    public function getCompletableFactors($inputs);

    public function getFactorByCode($code, $user, $select = [], $relation = []);

    public function editFactor($factor, $inputs);

    public function changeStatusFactor($factor, $inputs);

    public function addFactor($inputs, $user);

    public function deleteFactor($factor);
}
