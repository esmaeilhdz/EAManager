<?php

namespace App\Http\Controllers;

use App\Facades\PaymentFacade;
use App\Http\Requests\Payment\PaymentAddRequest;
use App\Http\Requests\Payment\PaymentDetailRequest;
use App\Http\Requests\Payment\PaymentEditRequest;
use App\Http\Requests\Payment\PaymentListRequest;
use App\Http\Requests\Payment\PaymentResourceDeleteRequest;
use App\Http\Requests\Payment\PaymentResourceListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use Common;

    /**
     * سرویس لیست همه پرداخت ها
     * @param PaymentListRequest $request
     * @return JsonResponse
     */
    public function getPayments(PaymentListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PaymentFacade::getPayments($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس لیست پرداخت های یک منبع
     * @param PaymentResourceListRequest $request
     * @return JsonResponse
     */
    public function getPaymentsResource(PaymentResourceListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PaymentFacade::getPaymentsResource($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات پرداخت
     * @param PaymentDetailRequest $request
     * @return JsonResponse
     */
    public function getPaymentDetail(PaymentDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PaymentFacade::getPaymentDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش پرداخت
     * @param PaymentEditRequest $request
     * @return JsonResponse
     */
    public function editPayment(PaymentEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PaymentFacade::editPayment($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن پرداخت
     * @param PaymentAddRequest $request
     * @return JsonResponse
     */
    public function addPayment(PaymentAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PaymentFacade::addPayment($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف پرداخت
     * @param PaymentDetailRequest $request
     * @return JsonResponse
     */
    public function deletePayment(PaymentDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PaymentFacade::deletePayment($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف پرداخت
     * @param PaymentDetailRequest $request
     * @return JsonResponse
     */
    public function deletePaymentsResource(PaymentResourceDeleteRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PaymentFacade::deletePaymentsResource($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
