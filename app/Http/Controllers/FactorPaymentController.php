<?php

namespace App\Http\Controllers;

use App\Facades\FactorPaymentFacade;
use App\Http\Requests\FactorPayment\FactorPaymentAddRequest;
use App\Http\Requests\FactorPayment\FactorPaymentDetailRequest;
use App\Http\Requests\FactorPayment\FactorPaymentListRequest;
use App\Traits\Common;

class FactorPaymentController extends Controller
{
    use Common;

    public function getFactorPayments(FactorPaymentListRequest $request)
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = FactorPaymentFacade::getFactorPayments($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function getFactorPaymentDetail(FactorPaymentDetailRequest $request)
    {
        $inputs = $request->validated();

        $result = FactorPaymentFacade::getFactorPaymentDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function addFactorPayment(FactorPaymentAddRequest $request)
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = FactorPaymentFacade::addFactorPayment($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function deleteFactorPayment(FactorPaymentDetailRequest $request)
    {
        $inputs = $request->validated();

        $result = FactorPaymentFacade::deleteFactorPayment($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
