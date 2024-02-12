<?php

namespace App\Http\Controllers;

use App\Facades\FactorItemFacade;
use App\Http\Requests\FactorItem\FactorItemAddRequest;
use App\Http\Requests\FactorItem\FactorItemDetailRequest;
use App\Http\Requests\FactorItem\FactorItemListRequest;

class FactorItemController extends Controller
{
    public function getFactorItems(FactorItemListRequest $request)
    {
        $inputs = $request->validated();

        $result = FactorItemFacade::getFactorItems($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function getFactorItemDetail(FactorItemDetailRequest $request)
    {
        $inputs = $request->validated();

        $result = FactorItemFacade::getFactorItemDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function addFactorItem(FactorItemAddRequest $request)
    {
        $inputs = $request->validated();

        $result = FactorItemFacade::addFactorItem($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function deleteFactorItem(FactorItemDetailRequest $request)
    {
        $inputs = $request->validated();

        $result = FactorItemFacade::deleteFactorItem($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
