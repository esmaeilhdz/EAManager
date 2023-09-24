<?php

namespace App\Http\Controllers;

use App\Facades\ProductAccessoryFacade;
use App\Http\Requests\ProductAccessory\ProductAccessoryListRequest;
use App\Traits\Common;

class ProductAccessoryController extends Controller
{
    use Common;

    public function getProductAccessories(ProductAccessoryListRequest $request)
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductAccessoryFacade::getProductAccessories($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
