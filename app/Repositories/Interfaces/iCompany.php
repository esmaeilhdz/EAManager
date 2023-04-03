<?php

namespace App\Repositories\Interfaces;

interface iCompany
{
    public function getCompanies($inputs, $user);

    public function getCompanyCombo($inputs, $user);

    public function getCompanyByCode($code, $select = [], $relation = []);

    public function editCompany($company, $inputs);

    public function addCompany($inputs, $user);

    public function deleteCompany($company);
}
