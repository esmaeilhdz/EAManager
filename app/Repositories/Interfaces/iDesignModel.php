<?php

namespace App\Repositories\Interfaces;

interface iDesignModel
{
    public function getDesignModels($inputs, $user);

    public function getDesignModelById($id, $user, $select = [], $relation = []);

    public function editDesignModel($design_model, $inputs);

    public function confirmDesignModel($design_model, $inputs);

    public function addDesignModel($inputs, $user);

    public function deleteDesignModel($design_model);
}
