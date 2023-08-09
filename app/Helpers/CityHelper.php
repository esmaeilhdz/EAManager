<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Facades\ProductWarehouseFacade;
use App\Repositories\Interfaces\iCity;
use App\Repositories\Interfaces\iCustomer;
use App\Repositories\Interfaces\iFactor;
use App\Repositories\Interfaces\iFactorPayment;
use App\Repositories\Interfaces\iFactorProduct;
use App\Repositories\Interfaces\iMenu;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Repositories\Interfaces\iProvince;
use App\Repositories\Interfaces\iRequestProductWarehouse;
use App\Traits\Common;
use App\Traits\FactorTrait;
use App\Traits\RequestProductWarehouseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CityHelper
{
    use Common;

    // attributes
    public iCity $city_interface;

    public function __construct(iCity $city_interface)
    {
        $this->city_interface = $city_interface;
    }

    /**
     * شهر ها
     * @param $inputs
     * @return array
     */
    public function getCities($inputs): array
    {
        $cities = $this->city_interface->getCities($inputs['province_id']);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $cities
        ];
    }

}
