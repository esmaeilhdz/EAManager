<?php

namespace App\Http\Controllers;

use App\Facades\FactorFacade;
use App\Http\Requests\Factor\FactorAddRequest;
use App\Http\Requests\Factor\FactorChangeStatusRequest;
use App\Http\Requests\Factor\FactorDetailRequest;
use App\Http\Requests\Factor\FactorEditRequest;
use App\Http\Requests\Factor\FactorListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class FactorController extends Controller
{
    use Common;

    /**
     * سرویس لیست فاکتور ها
     * @param FactorListRequest $request
     * @return JsonResponse
     */
    public function getFactors(FactorListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = FactorFacade::getFactors($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function getCompletableFactors(FactorListRequest $request)
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = FactorFacade::getCompletableFactors($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات فاکتور
     * @param FactorDetailRequest $request
     * @return JsonResponse
     */
    public function getFactorDetail(FactorDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = FactorFacade::getFactorDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش فاکتور
     * @param FactorEditRequest $request
     * @return JsonResponse
     */
    public function editFactor(FactorEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = FactorFacade::editFactor($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس تغییر وضعیت تکمیل بودن فاکتور
     * @param FactorChangeStatusRequest $request
     * @return JsonResponse
     */
    public function changeStatusFactor(FactorChangeStatusRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = FactorFacade::changeStatusFactor($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن فاکتور
     * @param FactorAddRequest $request
     * @return JsonResponse
     */
    public function addFactor(FactorAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = FactorFacade::addFactor($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف فاکتور
     * @param FactorDetailRequest $request
     * @return JsonResponse
     */
    public function deleteFactor(FactorDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = FactorFacade::deleteFactor($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
