<?php

namespace App\Repositories\Interfaces;

interface iSewing
{
    public function getSewings($inputs);

    public function getSewingById($inputs, $select = [], $relation = []);

    public function editSewing($sewing, $inputs);

    public function addSewing($inputs, $user);

    public function deleteSewing($sewing);
}
