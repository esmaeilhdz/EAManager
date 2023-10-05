<?php

namespace App\Http\Controllers;

use App\Facades\AccessoryWarehouseFacade;
use App\Http\Requests\AccessoryBuy\AccessoryBuyListRequest;
use App\Traits\Common;

class AccessoryWarehouseController extends Controller
{
    use Common;

    public function getAccessoryWarehouses(AccessoryBuyListRequest $request)
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccessoryWarehouseFacade::getAccessoryWarehouses($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
