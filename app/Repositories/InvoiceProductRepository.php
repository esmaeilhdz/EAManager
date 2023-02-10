<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InvoiceProductRepository implements Interfaces\iInvoiceProduct
{
    use Common;

    public function getById($invoice_id, $id, $select = [], $relation = [])
    {
        try {
            $invoice_product = InvoiceProduct::where('invoice_id', $invoice_id)
                ->where('id', $id);

            if ($select) {
                $invoice_product = $invoice_product->select($select);
            }

            if ($relation) {
                $invoice_product = $invoice_product->with($relation);
            }

            return $invoice_product->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش پیش فاکتور
     * @param $invoice
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editInvoiceProduct($invoice_product, $inputs): mixed
    {
        try {
            $invoice_product->product_warehouse_id = $inputs['product_warehouse_id'];
            $invoice_product->free_size_count = $inputs['free_size_count'];
            $invoice_product->size1_count = $inputs['size1_count'];
            $invoice_product->size2_count = $inputs['size2_count'];
            $invoice_product->size3_count = $inputs['size3_count'];
            $invoice_product->size4_count = $inputs['size4_count'];

            return $invoice_product->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن محصولات پیش فاکتور
     * @param array $inputs
     * @param $invoice_id
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addInvoiceProduct(array $inputs, $invoice_id, $user): array
    {
        try {
            $invoice_product = new InvoiceProduct();

            $invoice_product->invoice_id = $invoice_id;
            $invoice_product->product_warehouse_id = $inputs['product_warehouse_id'];
            $invoice_product->free_size_count = $inputs['free_size_count'];
            $invoice_product->size1_count = $inputs['size1_count'];
            $invoice_product->size2_count = $inputs['size2_count'];
            $invoice_product->size3_count = $inputs['size3_count'];
            $invoice_product->size4_count = $inputs['size4_count'];

            $result = $invoice_product->save();

            return [
                'result' => $result,
                'data' => $result ? $invoice_product->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف پیش فاکتور
     * @param $invoice
     * @return mixed
     * @throws ApiException
     */
    public function deleteInvoiceProduct($invoice_id): mixed
    {
        try {
            return InvoiceProduct::where('invoice_id', $invoice_id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
