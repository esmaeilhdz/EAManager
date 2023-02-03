<?php

namespace App\Http\Controllers;

use App\Facades\PeriodSaleFacade;
use App\Http\Requests\PeriodSale\PeriodSaleAddRequest;
use App\Http\Requests\PeriodSale\PeriodSaleDetailRequest;
use App\Http\Requests\PeriodSale\PeriodSaleEditRequest;
use App\Http\Requests\PeriodSale\PeriodSaleListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class PeriodSaleController extends Controller
{
    use Common;

    /**
     * سرویس لیست دوره های فروش
     * @param PeriodSaleListRequest $request
     * @return JsonResponse
     */
    public function getPeriodSales(PeriodSaleListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PeriodSaleFacade::getPeriodSales($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات دوره فروش
     * @param PeriodSaleDetailRequest $request
     * @return JsonResponse
     */
    public function getPeriodSaleDetail(PeriodSaleDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PeriodSaleFacade::getPeriodSaleDetail($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش دوره فروش
     * @param PeriodSaleEditRequest $request
     * @return JsonResponse
     */
    public function editPeriodSale(PeriodSaleEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PeriodSaleFacade::editPeriodSale($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن دوره فروش
     * @param PeriodSaleAddRequest $request
     * @return JsonResponse
     */
    public function addPeriodSale(PeriodSaleAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PeriodSaleFacade::addPeriodSale($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف دوره فروش
     * @param PeriodSaleDetailRequest $request
     * @return JsonResponse
     */
    public function deletePeriodSale(PeriodSaleDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PeriodSaleFacade::deletePeriodSale($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
