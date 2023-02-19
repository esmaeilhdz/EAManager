<?php

namespace App\Repositories\Interfaces;

interface iPerson
{
    public function getPersons($inputs, $user);

    public function getPersonByCode($code, $select = []);

    public function editPerson($inputs);

    public function addPerson($inputs, $user);

    public function deletePerson($id);
}
