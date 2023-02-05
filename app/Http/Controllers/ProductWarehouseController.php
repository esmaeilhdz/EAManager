<?php

namespace App\Http\Controllers;

use App\Facades\ProductWarehouseFacade;
use App\Http\Requests\ProductWarehouse\ProductWarehouseAddRequest;
use App\Http\Requests\ProductWarehouse\ProductWarehouseDetailRequest;
use App\Http\Requests\ProductWarehouse\ProductWarehouseEditRequest;
use App\Http\Requests\ProductWarehouse\ProductWarehouseListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ProductWarehouseController extends Controller
{
    use Common;

    /**
     * سرویس لیست انبار های کالا
     * @param ProductWarehouseListRequest $request
     * @return JsonResponse
     */
    public function getProductWarehouses(ProductWarehouseListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductWarehouseFacade::getProductWarehouses($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data'], $result['other']);
    }

    /**
     * سرویس جزئیات انبار کالا
     * @param ProductWarehouseDetailRequest $request
     * @return JsonResponse
     */
    public function getProductWarehouseDetail(ProductWarehouseDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductWarehouseFacade::getProductWarehouseDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش انبار کالا
     * @param ProductWarehouseEditRequest $request
     * @return JsonResponse
     */
    public function editProductWarehouse(ProductWarehouseEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductWarehouseFacade::editProductWarehouse($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن انبار کالا
     * @param ProductWarehouseAddRequest $request
     * @return JsonResponse
     */
    public function addProductWarehouse(ProductWarehouseAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductWarehouseFacade::addProductWarehouse($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف انبار کالا
     * @param ProductWarehouseDetailRequest $request
     * @return JsonResponse
     */
    public function deleteProductWarehouse(ProductWarehouseDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductWarehouseFacade::deleteProductWarehouse($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
