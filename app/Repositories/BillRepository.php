<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Bill;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BillRepository implements Interfaces\iBill
{
    use Common;

    /**
     * لیست دوره های فروش
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getBills($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Bill::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'bill_type:enum_id,enum_caption',
            ])
                ->select([
                    'id',
                    'bill_type_id',
                    'bill_id',
                    'payment_id',
                    'created_by',
                    'created_at'
                ])
                ->where('company_id', $company_id)
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getBillById($id, $user, $select = [], $relation = [])
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $bill = Bill::where('id', $id)
                ->where('company_id', $company_id);

            if (count($select)) {
                $bill = $bill->select($select);
            }

            if (count($relation)) {
                $bill = $bill->with($relation);
            }

            return $bill->first();

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editBill($bill, $inputs)
    {
        try {
            $bill->bill_type_id = $inputs['bill_type_id'];
            $bill->bill_id = $inputs['bill_id'];
            $bill->payment_id = $inputs['bill_type_id'];

            return $bill->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addBill($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $bill = new Bill();

            $bill->company_id = $company_id;
            $bill->bill_type_id = $inputs['bill_type_id'];
            $bill->bill_id = $inputs['bill_id'];
            $bill->payment_id = $inputs['payment_id'];
            $bill->created_by = $user->id;

            $result = $bill->save();

            return [
                'result' => $result,
                'data' => $result ? $bill->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteBill($id)
    {
        try {
            return Bill::where('id', $id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
