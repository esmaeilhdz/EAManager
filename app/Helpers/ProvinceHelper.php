<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Facades\ProductWarehouseFacade;
use App\Repositories\Interfaces\iCustomer;
use App\Repositories\Interfaces\iFactor;
use App\Repositories\Interfaces\iFactorPayment;
use App\Repositories\Interfaces\iFactorItem;
use App\Repositories\Interfaces\iMenu;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Repositories\Interfaces\iProvince;
use App\Repositories\Interfaces\iRequestProductWarehouse;
use App\Traits\Common;
use App\Traits\FactorTrait;
use App\Traits\RequestProductWarehouseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProvinceHelper
{
    use Common;

    // attributes
    public iProvince $province_interface;

    public function __construct(iProvince $province_interface)
    {
        $this->province_interface = $province_interface;
    }

    /**
     * استان ها
     * @return array
     */
    public function getProvinces(): array
    {
        $provinces = $this->province_interface->getProvinces();

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $provinces
        ];
    }

}
