<?php

namespace App\Http\Controllers;

use App\Facades\WarehouseFacade;
use App\Http\Requests\Warehouse\WarehouseAddRequest;
use App\Http\Requests\Warehouse\WarehouseDetailRequest;
use App\Http\Requests\Warehouse\WarehouseEditRequest;
use App\Http\Requests\Warehouse\WarehouseListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    use Common;

    /**
     * سرویس لیست انبارها
     * @param WarehouseListRequest $request
     * @return JsonResponse
     */
    public function getWarehouses(WarehouseListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = WarehouseFacade::getWarehouses($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات انبار
     * @param WarehouseDetailRequest $request
     * @return JsonResponse
     */
    public function getWarehouseDetail(WarehouseDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = WarehouseFacade::getWarehouseDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش انبار
     * @param WarehouseEditRequest $request
     * @return JsonResponse
     */
    public function editWarehouse(WarehouseEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = WarehouseFacade::editWarehouse($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن انبار
     * @param WarehouseAddRequest $request
     * @return JsonResponse
     */
    public function addWarehouse(WarehouseAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = WarehouseFacade::addWarehouse($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف انبار
     * @param WarehouseDetailRequest $request
     * @return JsonResponse
     */
    public function deleteWarehouse(WarehouseDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = WarehouseFacade::deleteWarehouse($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
