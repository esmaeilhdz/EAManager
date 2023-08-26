<?php

namespace App\Http\Controllers;

use App\Facades\FactorProductFacade;
use App\Http\Requests\FactorPayment\FactorPaymentAddRequest;
use App\Http\Requests\FactorPayment\FactorPaymentDetailRequest;
use App\Http\Requests\FactorProduct\FactorProductDetailRequest;
use App\Http\Requests\FactorProduct\FactorProductListRequest;

class FactorProductController extends Controller
{
    public function getFactorProducts(FactorProductListRequest $request)
    {
        $inputs = $request->validated();

        $result = FactorProductFacade::getFactorProducts($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function getFactorProductDetail(FactorProductDetailRequest $request)
    {
        $inputs = $request->validated();

        $result = FactorProductFacade::getFactorProductDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function addFactorProduct(FactorPaymentAddRequest $request)
    {
        $inputs = $request->validated();

        $result = FactorProductFacade::addFactorProduct($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function deleteFactorProduct(FactorProductDetailRequest $request)
    {
        $inputs = $request->validated();

        $result = FactorProductFacade::deleteFactorProduct($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
