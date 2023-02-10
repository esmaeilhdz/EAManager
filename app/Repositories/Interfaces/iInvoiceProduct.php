<?php

namespace App\Repositories\Interfaces;

interface iInvoiceProduct
{

    public function getById($invoice_id, $id, $select = [], $relation = []);

    public function editInvoiceProduct($invoice_product, $inputs);

    public function addInvoiceProduct(array $inputs, $invoice_id, $user);

    public function deleteInvoiceProduct($invoice_id);
}
