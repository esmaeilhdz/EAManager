<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Repositories\Interfaces\iSalePeriod;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class SalePeriodHelper
{
    use Common;

    // attributes
    public iSalePeriod $sale_period_interface;

    public function __construct(iSalePeriod $sale_period_interface)
    {
        $this->sale_period_interface = $sale_period_interface;
    }

    /**
     * لیست دوره های فروش
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function getSalePeriods($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'sale_periods');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $sale_periods = $this->sale_period_interface->getSalePeriods($inputs);

        $sale_periods->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'start_date' => $item->start_date,
                'end_date' => $item->end_date,
                'creator' => is_null($item->creator->person) ? null : [
                    'person' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $sale_periods
        ];
    }

    /**
     * جزئیات دوره فروش
     * @param $id
     * @return array|void
     */
    public function getSalePeriodDetail($id): array
    {
        $sale_period = $this->sale_period_interface->getSalePeriodById($id);
        if (is_null($sale_period)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $sale_period
        ];
    }

    /**
     * جزئیات دوره فروش
     * @param $inputs
     * @return array
     */
    public function getSalePeriodCombo($inputs): array
    {
        $user = Auth::user();
        $sale_periods = $this->sale_period_interface->getSalePeriodsCombo($inputs, $user);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $sale_periods
        ];
    }

    /**
     * ویرایش دوره فروش
     * @param $inputs
     * @return array
     */
    public function editSalePeriod($inputs): array
    {
        $place = $this->sale_period_interface->getSalePeriodById($inputs['id']);
        if (is_null($place)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->sale_period_interface->editSalePeriod($inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن دوره فروش
     * @param $inputs
     * @return array
     */
    public function addSalePeriod($inputs): array
    {
        $user = Auth::user();
        $result = $this->sale_period_interface->addSalePeriod($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function deleteSalePeriod($id): array
    {
        $sale_period = $this->sale_period_interface->getSalePeriodById($id);
        if (is_null($sale_period)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->sale_period_interface->deleteSalePeriod($id);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
