<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\PeriodSale;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PeriodSaleRepository implements Interfaces\iPeriodSale
{
    use Common;

    /**
     * لیست دوره های فروش
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getPeriodSales($inputs): LengthAwarePaginator
    {
        try {
            return PeriodSale::with([
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

    public function getPeriodSaleById($id)
    {
        try {
            return PeriodSale::query()
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

    public function editPeriodSale($inputs)
    {
        try {
            return PeriodSale::where('id', $inputs['id'])
                ->update([
                    'name' => $inputs['name'],
                    'start_date' => $inputs['start_date'],
                    'end_date' => $inputs['end_date']
                ]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addPeriodSale($inputs, $user)
    {
        try {
            $period_sale = new PeriodSale();

            $period_sale->name = $inputs['name'];
            $period_sale->start_date = $inputs['start_date'];
            $period_sale->end_date = $inputs['end_date'];
            $period_sale->created_by = $user->id;

            $result = $period_sale->save();

            return [
                'result' => $result,
                'data' => $result ? $period_sale->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deletePeriodSale($id)
    {
        try {
            return PeriodSale::where('id', $id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
