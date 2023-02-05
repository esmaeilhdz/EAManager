<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Address;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AddressRepository implements Interfaces\iAddress
{
    use Common;

    /**
     * جزئیات آدرس
     * @param $inputs
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getAddressById($inputs, $select = [], $relation = []): mixed
    {
        try {
            $address = Address::where('model_type', $inputs['model_type'])
                ->where('model_id', $inputs['model_id'])
                ->where('id', $inputs['id']);

            if (count($relation)) {
                $address = $address->with($relation);
            }


            if (count($select)) {
                $address = $address->select($select);
            }

            return $address->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش آدرس
     * @param $address
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editAddress($address, $inputs): mixed
    {
        try {
            $address->province_id = $inputs['province_id'];
            $address->city_id = $inputs['city_id'];
            $address->address_kind_id = $inputs['address_kind_id'];
            $address->address = $inputs['address'];
            $address->tel = $inputs['tel'];

            return $address->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن آدرس
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addAddress($inputs, $user): array
    {
        try {
            $address = new Address();

            $address->model_type = $inputs['model_type'];
            $address->model_id = $inputs['model_id'];
            $address->province_id = $inputs['province_id'];
            $address->city_id = $inputs['city_id'];
            $address->address_kind_id = $inputs['address_kind_id'];
            $address->address = $inputs['address'];
            $address->tel = $inputs['tel'];
            $address->created_by = $user->id;

            $result = $address->save();

            return [
                'result' => $result,
                'data' => $result ? $address->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف آدرس
     * @param $address
     * @return mixed
     * @throws ApiException
     */
    public function deleteAddress($address): mixed
    {
        try {
            return $address->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
