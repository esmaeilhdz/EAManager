<?php

namespace App\Repositories\Interfaces;

interface iEnumeration
{
    public function getEnumerations($inputs);

    public function getEnumerationByCategory($category_name, array $select = []);

    public function getEnumerationDetail($inputs, array $select = []);

    public function getEnumerationById($id, array $select = []);

    public function getEnumerationAll();

    public function editEnumeration($enumeration, $inputs);

    public function addEnumeration($enumeration, $inputs, $user);

    public function deleteEnumeration($enumeration);
}
