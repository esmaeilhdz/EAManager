<?php

namespace App\Repositories\Interfaces;

interface iAccount
{
    public function getAccounts($inputs);

    public function getAccountByCode($code);

    public function editAccount($inputs);

    public function addAccount($inputs, $user);

    public function deleteAccount($code);
}
