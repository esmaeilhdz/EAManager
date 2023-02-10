<?php

namespace App\Repositories\Interfaces;

interface iInvoice
{
    public function getInvoices($inputs);

    public function getInvoiceByCode($code, $select = [], $relation = []);

    public function editInvoice($invoice, $inputs);

    public function addInvoice($inputs, $user);

    public function deleteInvoice($invoice);
}
