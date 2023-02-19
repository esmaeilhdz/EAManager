<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Invoice;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InvoiceRepository implements Interfaces\iInvoice
{
    use Common;

    /**
     * لیست پیش فاکتورها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getInvoices($inputs): LengthAwarePaginator
    {
        try {
            return Invoice::query()
                ->with([
                    'customer:id,name,mobile',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'code',
                    'customer_id',
                    'name',
                    'mobile',
                    'final_price',
                    'created_by',
                    'created_at'
                ])
                ->where(function ($q) use ($inputs) {
                    $q->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                        ->orWhereHas('customer', function ($q2) use ($inputs) {
                            $q2->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params']);
                        });
                })
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات پیش فاکتور
     * @param $code
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getInvoiceByCode($code, $select = [], $relation = []): mixed
    {
        try {
            $invoice = Invoice::whereCode($code);

            if (count($relation)) {
                $invoice = $invoice->with($relation);
            }

            if (count($select)) {
                $invoice = $invoice->select($select);
            }

            return $invoice->first();
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
    public function editInvoice($invoice, $inputs): mixed
    {
        try {
            if (isset($inputs['customer_id'])) {
                $invoice->customer_id = $inputs['customer_id'];
            }
            if (isset($inputs['name'])) {
                $invoice->name = $inputs['name'];
            }
            if (isset($inputs['mobile'])) {
                $invoice->mobile = $inputs['mobile'];
            }
            $invoice->final_price = $inputs['final_price'];

            return $invoice->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن پیش فاکتور
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addInvoice($inputs, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $invoice = new Invoice();

            $invoice->code = $this->randomString();
            $invoice->company_id = $company_id;
            $invoice->customer_id = $inputs['customer_id'] ?? null;
            $invoice->name = $inputs['name'] ?? null;
            $invoice->mobile = $inputs['mobile'] ?? null;
            $invoice->final_price = $inputs['final_price'];
            $invoice->created_by = $user->id;

            $result = $invoice->save();

            return [
                'result' => $result,
                'data' => $result ? [
                    'code' => $invoice->code,
                    'id' => $invoice->id
                ] : null
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
    public function deleteInvoice($invoice): mixed
    {
        try {
            return $invoice->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
