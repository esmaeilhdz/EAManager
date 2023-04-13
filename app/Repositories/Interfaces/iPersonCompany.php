<?php

namespace App\Repositories\Interfaces;

interface iPersonCompany
{
    public function getCompaniesOfPerson($inputs, $user);

    public function getPersonCompanyDetail($person_id, $company_id);

    public function editPersonCompany($inputs, $person_company);

    public function changePersonCompany($inputs, $person_company);

    public function addPersonCompany($inputs, $user);

    public function deletePersonCompany($person_company);
}
