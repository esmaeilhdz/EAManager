<?php

namespace App\Repositories\Interfaces;

interface iCutting
{
    public function getCuttings($inputs);

    public function getCuttingById($inputs, $select = [], $relation = []);

    public function editCutting($cutting, $inputs);

    public function addCutting($inputs, $user);

    public function deleteCutting($cutting);
}
