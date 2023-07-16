<?php

namespace App\Http\Controllers;

use App\Facades\ProductPriceFacade;
use App\Http\Requests\ProductPrice\ProductPriceAddRequest;
use App\Http\Requests\ProductPrice\ProductPriceDetailRequest;
use App\Http\Requests\ProductPrice\ProductPriceEditRequest;
use App\Http\Requests\ProductPrice\ProductPriceListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ProductPriceController extends Controller
{
    use Common;

    /**
     * سرویس لیست قیمت های کالا
     * @param ProductPriceListRequest $request
     * @return JsonResponse
     */
    public function getProductPrices(ProductPriceListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductPriceFacade::getProductPrices($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data'], $result['other']);
    }

    /**
     * سرویس جزئیات قیمت کالا
     * @param ProductPriceDetailRequest $request
     * @return JsonResponse
     */
    public function getProductPriceDetail(ProductPriceDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductPriceFacade::getProductPriceDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش قیمت کالا
     * @param ProductPriceEditRequest $request
     * @return JsonResponse
     */
    public function editProductPrice(ProductPriceEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductPriceFacade::editProductPrice($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن قیمت کالا
     * @param ProductPriceAddRequest $request
     * @return JsonResponse
     */
    public function addProductPrice(ProductPriceAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductPriceFacade::addProductPrice($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف قیمت کالا
     * @param ProductPriceDetailRequest $request
     * @return JsonResponse
     */
    public function deleteProductPrice(ProductPriceDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = ProductPriceFacade::deleteProductPrice($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
