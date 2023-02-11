<?php

namespace App\Http\Controllers;

use App\Facades\SalePeriodFacade;
use App\Http\Requests\SalePeriod\SalePeriodAddRequest;
use App\Http\Requests\SalePeriod\SalePeriodDetailRequest;
use App\Http\Requests\SalePeriod\SalePeriodEditRequest;
use App\Http\Requests\SalePeriod\SalePeriodListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class SalePeriodController extends Controller
{
    use Common;

    /**
     * سرویس لیست دوره های فروش
     * @param SalePeriodListRequest $request
     * @return JsonResponse
     */
    public function getSalePeriods(SalePeriodListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalePeriodFacade::getSalePeriods($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات دوره فروش
     * @param SalePeriodDetailRequest $request
     * @return JsonResponse
     */
    public function getSalePeriodDetail(SalePeriodDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalePeriodFacade::getSalePeriodDetail($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش دوره فروش
     * @param SalePeriodEditRequest $request
     * @return JsonResponse
     */
    public function editSalePeriod(SalePeriodEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalePeriodFacade::editSalePeriod($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن دوره فروش
     * @param SalePeriodAddRequest $request
     * @return JsonResponse
     */
    public function addSalePeriod(SalePeriodAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalePeriodFacade::addSalePeriod($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف دوره فروش
     * @param SalePeriodDetailRequest $request
     * @return JsonResponse
     */
    public function deleteSalePeriod(SalePeriodDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalePeriodFacade::deleteSalePeriod($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
