<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\SalePeriod;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SalePeriodRepository implements Interfaces\iSalePeriod
{
    use Common;

    /**
     * لیست دوره های فروش
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getSalePeriods($inputs): LengthAwarePaginator
    {
        try {
            return SalePeriod::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
            ])
                ->select([
                    'id',
                    'name',
                    'start_date',
                    'end_date',
                    'created_by',
                    'created_at'
                ])
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getSalePeriodById($id)
    {
        try {
            return SalePeriod::query()
                ->select([
                    'id',
                    'name',
                    'start_date',
                    'end_date'
                ])
                ->find($id);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getSalePeriodsCombo($inputs)
    {
        try {
            return SalePeriod::select('id', 'name')
                ->when(isset($inputs['search_txt']), function ($q) use ($inputs) {
                    $q->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                })
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editSalePeriod($inputs)
    {
        try {
            return SalePeriod::where('id', $inputs['id'])
                ->update([
                    'name' => $inputs['name'],
                    'start_date' => $inputs['start_date'],
                    'end_date' => $inputs['end_date']
                ]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addSalePeriod($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $sale_period = new SalePeriod();

            $sale_period->company_id = $company_id;
            $sale_period->name = $inputs['name'];
            $sale_period->start_date = $inputs['start_date'];
            $sale_period->end_date = $inputs['end_date'];
            $sale_period->created_by = $user->id;

            $result = $sale_period->save();

            return [
                'result' => $result,
                'data' => $result ? $sale_period->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteSalePeriod($id)
    {
        try {
            return SalePeriod::where('id', $id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
