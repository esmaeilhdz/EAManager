<?php

namespace App\Repositories\Interfaces;

interface iCloth
{
    public function getClothes($inputs);

    public function getClothByCode($code);

    public function editCloth($inputs);

    public function addCloth($inputs, $user);

    public function deleteCloth($code);
}
