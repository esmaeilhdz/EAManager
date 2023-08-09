<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Models\Province;
use App\Models\Sewing;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProvinceRepository implements Interfaces\iProvince
{
    use Common;

    /**
     * استان ها
     * @throws ApiException
     */
    public function getProvinces(): \Illuminate\Database\Eloquent\Collection|array
    {
        try {
            return Province::query()
                ->select('id', 'name')
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

}
