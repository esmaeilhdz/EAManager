<?php

namespace App\Repositories\Interfaces;

interface iPersonCompany
{
    public function getCompaniesOfPerson($inputs, $user);

    public function addPersonCompany($inputs, $user);
}
