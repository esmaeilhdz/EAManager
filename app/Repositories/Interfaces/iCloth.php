<?php

namespace App\Repositories\Interfaces;

interface iCloth
{
    public function getClothes($inputs);

    public function getClothByCode($code);

    public function editCloth($cloth, $inputs);

    public function addCloth($inputs, $user, $company_id);

    public function deleteCloth($code);
}
