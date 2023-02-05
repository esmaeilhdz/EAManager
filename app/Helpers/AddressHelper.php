<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iAddress;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class AddressHelper
{
    use Common;

    // attributes
    public iAddress $address_interface;

    public function __construct(iAddress $address_interface)
    {
        $this->address_interface = $address_interface;
    }

    /**
     * ویرایش آدرس
     * @param $inputs
     * @return array
     */
    public function editAddress($inputs): array
    {
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        if ($inputs['model_id'] == 0) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $select = ['id', 'province_id', 'city_id', 'address', 'tel'];
        $address = $this->address_interface->getAddressById($inputs, $select);
        if (is_null($address)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->address_interface->editAddress($address, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن آدرس
     * @param $inputs
     * @return array
     */
    public function addAddress($inputs): array
    {
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        if ($inputs['model_id'] == 0) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $user = Auth::user();
        $result = $this->address_interface->addAddress($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * حذف آدرس
     * @param $code
     * @return array
     */
    public function deleteAddress($inputs): array
    {
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        if ($inputs['model_id'] == 0) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $select = ['id'];
        $address = $this->address_interface->getAddressById($inputs, $select);
        if (is_null($address)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->address_interface->deleteAddress($address);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
