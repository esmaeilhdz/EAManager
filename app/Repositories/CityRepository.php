<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\City;
use App\Models\Product;
use App\Models\Province;
use App\Models\Sewing;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CityRepository implements Interfaces\iCity
{
    use Common;

    /**
     * استان ها
     * @throws ApiException
     */
    public function getCities($province_id): \Illuminate\Database\Eloquent\Collection|array
    {
        try {
            return City::query()
                ->select('id', 'name')
                ->where('province_id', $province_id)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

}
