<?php

namespace App\Repositories\Interfaces;

interface iCloth
{
    public function getClothes($inputs, $user);

    public function getClothByCode($code, $user);

    public function getClothCombo($inputs, $user);

    public function editCloth($cloth, $inputs);

    public function addCloth($inputs, $user);

    public function deleteCloth($code);
}
