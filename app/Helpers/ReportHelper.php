<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iReport;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class ReportHelper
{
    use Common;

    // attributes
    public iReport $report_interface;

    public function __construct(iReport $report_interface)
    {
        $this->report_interface = $report_interface;
    }

    /**
     * سرویس لیست مشتریان بدهکار
     * @param $inputs
     * @return array
     */
    public function getDebtorCustomers($inputs): array
    {
        $user = Auth::user();
        $inputs['order_by'] = $this->orderBy($inputs, 'customers');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $customers = $this->report_interface->getDebtorCustomers($inputs, $user);

        $customers->transform(function ($item) {
            $factors = null;
            foreach ($item->factor as $factor) {
                $factors[] = [
                    'factor_no' => $factor->factor_no,
                    'final_price' => $factor->final_price
                ];
            }
            return [
                'code' => $item->code,
                'name' => $item->name,
                'mobile' => $item->mobile,
                'score' => $item->score,
                'factors' => $factors
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $customers
        ];
    }

}
