<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPeriodSale;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class PeriodSaleHelper
{
    use Common;

    // attributes
    public iPeriodSale $period_sale_interface;

    public function __construct(iPeriodSale $period_sale_interface)
    {
        $this->period_sale_interface = $period_sale_interface;
    }

    /**
     * لیست دوره های فروش
     * @param $inputs
     * @return array
     */
    public function getPeriodSales($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $period_sales = $this->period_sale_interface->getPeriodSales($inputs);

        $period_sales->transform(function ($item) {
            return [
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
            'data' => $period_sales
        ];
    }

    /**
     * جزئیات دوره فروش
     * @param $id
     * @return array|void
     */
    public function getPeriodSaleDetail($id): array
    {
        $period_sale = $this->period_sale_interface->getPeriodSaleById($id);
        if (is_null($period_sale)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $period_sale
        ];
    }

    /**
     * ویرایش دوره فروش
     * @param $inputs
     * @return array
     */
    public function editPeriodSale($inputs): array
    {
        $place = $this->period_sale_interface->getPeriodSaleById($inputs['id']);
        if (is_null($place)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->period_sale_interface->editPeriodSale($inputs);
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
    public function addPeriodSale($inputs): array
    {
        $user = Auth::user();
        $result = $this->period_sale_interface->addPeriodSale($inputs, $user);
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
    public function deletePeriodSale($id): array
    {
        $period_sale = $this->period_sale_interface->getPeriodSaleById($id);
        if (is_null($period_sale)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->period_sale_interface->deletePeriodSale($id);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
