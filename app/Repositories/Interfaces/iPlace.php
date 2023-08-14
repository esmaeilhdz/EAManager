<?php

namespace App\Repositories\Interfaces;

interface iPlace
{
    public function getPlaces($inputs);

    public function getPlaceById($id);

    public function getPlaceCombo($inputs, $user);

    public function editPlace($inputs);

    public function addPlace($inputs, $user);

    public function deletePlace($id);
}
