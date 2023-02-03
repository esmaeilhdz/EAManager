<?php

namespace App\Http\Controllers;

use App\Facades\RequestProductWarehouseFacade;
use App\Http\Requests\RequestProductWarehouse\RequestProductWarehouseAddRequest;
use App\Http\Requests\RequestProductWarehouse\RequestProductWarehouseDetailRequest;
use App\Http\Requests\RequestProductWarehouse\RequestProductWarehouseEditRequest;
use App\Http\Requests\RequestProductWarehouse\RequestProductWarehouseListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class RequestProductWarehouseController extends Controller
{
    use Common;

    /**
     * سرویس لیست درخواست های کالا از انبار
     * @param RequestProductWarehouseListRequest $request
     * @return JsonResponse
     */
    public function getRequestProductWarehouses(RequestProductWarehouseListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = RequestProductWarehouseFacade::getRequestProductWarehouses($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data'], $result['other'] ?? []);
    }

    /**
     * سرویس جزئیات درخواست کالا از انبار
     * @param RequestProductWarehouseDetailRequest $request
     * @return JsonResponse
     */
    public function getRequestProductWarehouseDetail(RequestProductWarehouseDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = RequestProductWarehouseFacade::getRequestProductWarehouseDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش درخواست کالا از انبار
     * @param RequestProductWarehouseEditRequest $request
     * @return JsonResponse
     */
    public function editRequestProductWarehouse(RequestProductWarehouseEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = RequestProductWarehouseFacade::editRequestProductWarehouse($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس تایید درخواست کالا از انبار
     * @param RequestProductWarehouseDetailRequest $request
     * @return JsonResponse
     */
    public function confirmRequestProductWarehouse(RequestProductWarehouseDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = RequestProductWarehouseFacade::confirmRequestProductWarehouse($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن درخواست کالا از انبار
     * @param RequestProductWarehouseAddRequest $request
     * @return JsonResponse
     */
    public function addRequestProductWarehouse(RequestProductWarehouseAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = RequestProductWarehouseFacade::addRequestProductWarehouse($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف درخواست کالا از انبار
     * @param RequestProductWarehouseDetailRequest $request
     * @return JsonResponse
     */
    public function deleteRequestProductWarehouse(RequestProductWarehouseDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = RequestProductWarehouseFacade::deleteRequestProductWarehouse($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
