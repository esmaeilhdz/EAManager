<?php

namespace App\Helpers;

use App\Models\Customer;
use App\Repositories\Interfaces\iAddress;
use App\Repositories\Interfaces\iCustomer;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerHelper
{
    use Common;

    // attributes
    public iCustomer $customer_interface;
    public iAddress $address_interface;

    public function __construct(iCustomer $customer_interface, iAddress $address_interface)
    {
        $this->customer_interface = $customer_interface;
        $this->address_interface = $address_interface;
    }

    /**
     * لیست مشتری ها
     * @param $inputs
     * @return array
     */
    public function getCustomers($inputs): array
    {
        $inputs['order_by'] = $this->orderBy($inputs, 'customers');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $customers = $this->customer_interface->getCustomers($inputs);

        $customers->transform(function ($item) {
            return [
                'code' => $item->code,
                'name' => $item->name,
                'mobile' => $item->mobile,
                'score' => $item->score,
                'province' => $item->address[0]->province->name ?? null,
                'city' => $item->address[0]->city->name ?? null,
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
        $select = ['id', 'code', 'parent_id', 'name', 'mobile', 'score'];
        $relation = [
            'parent:id,name',
            'address:model_type,model_id,province_id,city_id,address',
            'address.province:id,name',
            'address.city:id,name',
        ];
        $customer = $this->customer_interface->getCustomerByCode($code, $select, $relation);
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

    public function getCustomerCombo($inputs)
    {
        $user = Auth::user();
        $customers = $this->customer_interface->getCustomersCombo($inputs, $user);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $customers
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

        $inputs['model_type'] = Customer::class;
        $inputs['model_id'] = $customer->id;
        $inputs['id'] = $inputs['address_id'];

        $address = $this->address_interface->getAddressById($inputs);
        if (is_null($address)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        DB::beginTransaction();
        $result[] = $this->customer_interface->editCustomer($customer, $inputs);
        $result[] = $this->address_interface->editAddress($address, $inputs);

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
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

        DB::beginTransaction();
        $res = $this->customer_interface->addCustomer($inputs, $user);

        $inputs['model_type'] = Customer::class;
        $inputs['model_id'] = $res['data']->id;
        $result[] = $res['result'];
        $result[] = $this->address_interface->addAddress($inputs, $user);

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => $res['data']->code
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
