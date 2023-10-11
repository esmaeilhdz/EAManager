<?php

namespace App\Http\Controllers;

use App\Facades\ProductAccessoryFacade;
use App\Http\Requests\ProductAccessory\ProductAccessoryAddRequest;
use App\Http\Requests\ProductAccessory\ProductAccessoryDetailRequest;
use App\Http\Requests\ProductAccessory\ProductAccessoryEditRequest;
use App\Http\Requests\ProductAccessory\ProductAccessoryListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ProductAccessoryController extends Controller
{
    use Common;

    /**
     * لیست خرج کار کالا
     * @param ProductAccessoryListRequest $request
     * @return JsonResponse
     */
    public function getProductAccessories(ProductAccessoryListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductAccessoryFacade::getProductAccessories($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * جزئیات خرج کار کالا
     * @param ProductAccessoryDetailRequest $request
     * @return JsonResponse
     */
    public function getProductAccessoryDetail(ProductAccessoryDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = ProductAccessoryFacade::getProductAccessoryDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * ویرایش خرج کار کالا
     * @param ProductAccessoryEditRequest $request
     * @return JsonResponse
     */
    public function editProductAccessory(ProductAccessoryEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductAccessoryFacade::editProductAccessory($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * افزودن خرج کار کالا
     * @param ProductAccessoryAddRequest $request
     * @return JsonResponse
     */
    public function addProductAccessory(ProductAccessoryAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductAccessoryFacade::addProductAccessory($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * حذف خرج کار کالا
     * @param ProductAccessoryDetailRequest $request
     * @return JsonResponse
     */
    public function deleteProductAccessory(ProductAccessoryDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductAccessoryFacade::deleteProductAccessory($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
