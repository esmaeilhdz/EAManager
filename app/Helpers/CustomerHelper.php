<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCustomer;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class CustomerHelper
{
    use Common;

    // attributes
    public iCustomer $customer_interface;

    public function __construct(iCustomer $customer_interface)
    {
        $this->customer_interface = $customer_interface;
    }

    /**
     * لیست مشتری ها
     * @param $inputs
     * @return array
     */
    public function getCustomers($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;mobile');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'customers');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $customers = $this->customer_interface->getCustomers($inputs);

        $customers->transform(function ($item) {
            return [
                'code' => $item->code,
                'name' => $item->name,
                'mobile' => $item->mobile,
                'score' => $item->score,
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
            'data' => $customers
        ];
    }

    /**
     * جزئیات مشتری
     * @param $code
     * @return array
     */
    public function getCustomerDetail($code): array
    {
        $customer = $this->customer_interface->getCustomerByCode($code);
        if (is_null($customer)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $customer
        ];
    }

    /**
     * ویرایش مشتری
     * @param $inputs
     * @return array
     */
    public function editCustomer($inputs): array
    {
        $customer = $this->customer_interface->getCustomerByCode($inputs['code']);
        if (is_null($customer)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->customer_interface->editCustomer($customer, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن مشتری
     * @param $inputs
     * @return array
     */
    public function addCustomer($inputs): array
    {
        $user = Auth::user();
        $result = $this->customer_interface->addCustomer($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * حذف مشتری
     * @param $code
     * @return array
     */
    public function deleteCustomer($code): array
    {
        $customer = $this->customer_interface->getCustomerByCode($code);
        if (is_null($customer)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->customer_interface->deleteCustomer($customer);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
