<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Customer;
use App\Models\Factor;
use App\Models\Product;
use App\Models\Sewing;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReportRepository implements Interfaces\iReport
{
    use Common;

    /**
     * لیست مشتریان بدهکار
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getDebtorCustomers($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Customer::with([
                'factor:customer_id,factor_no,final_price'
            ])
                ->select([
                    'id',
                    'code',
                    'name',
                    'mobile',
                    'score'
                ])
                ->whereHas('factor', function ($q) {
                    $q->where('status', 1)->whereNotNull('settlement_date');
                })
                ->where('company_id', $company_id)
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

}
