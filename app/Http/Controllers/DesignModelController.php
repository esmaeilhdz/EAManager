<?php

namespace App\Http\Controllers;

use App\Facades\DesignModelFacade;
use App\Http\Requests\DesignModel\DesignModelAddRequest;
use App\Http\Requests\DesignModel\DesignModelConfirmRequest;
use App\Http\Requests\DesignModel\DesignModelDetailRequest;
use App\Http\Requests\DesignModel\DesignModelEditRequest;
use App\Http\Requests\DesignModel\DesignModelListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class DesignModelController extends Controller
{
    use Common;

    /**
     * سرویس لیست طراحی مدل ها
     * @param DesignModelListRequest $request
     * @return JsonResponse
     */
    public function getDesignModels(DesignModelListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = DesignModelFacade::getDesignModels($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات طراحی مدل
     * @param DesignModelDetailRequest $request
     * @return JsonResponse
     */
    public function getDesignModelDetail(DesignModelDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = DesignModelFacade::getDesignModelDetail($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس تایید طراحی مدل
     * @param DesignModelConfirmRequest $request
     * @return JsonResponse
     */
    public function confirmDesignModel(DesignModelConfirmRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = DesignModelFacade::confirmDesignModel($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش طراحی مدل
     * @param DesignModelEditRequest $request
     * @return JsonResponse
     */
    public function editDesignModel(DesignModelEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = DesignModelFacade::editDesignModel($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن طراحی مدل
     * @param DesignModelAddRequest $request
     * @return JsonResponse
     */
    public function addDesignModel(DesignModelAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = DesignModelFacade::addDesignModel($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف طراحی مدل
     * @param DesignModelDetailRequest $request
     * @return JsonResponse
     */
    public function deleteDesignModel(DesignModelDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = DesignModelFacade::deleteDesignModel($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
