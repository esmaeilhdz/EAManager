<?php

namespace App\Repositories\Interfaces;

interface iPerson
{
    public function getPersons($inputs, $user);

    public function getPersonsCombo($inputs, $user);

    public function getPersonById($id, $select = [], $relation = []);

    public function getPersonByCode($code, $select = [], $relation = []);

    public function editPerson($inputs);

    public function addPerson($inputs, $user);

    public function deletePerson($id);
}
