<?php

namespace App\Http\Controllers;

use App\Facades\ProductFacade;
use App\Http\Requests\Product\ProductAddRequest;
use App\Http\Requests\Product\ProductDetailRequest;
use App\Http\Requests\Product\ProductEditRequest;
use App\Http\Requests\Product\ProductListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    use Common;

    /**
     * سرویس لیست کالا ها
     * @param ProductListRequest $request
     * @return JsonResponse
     */
    public function getProducts(ProductListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductFacade::getProducts($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات کالا
     * @param ProductDetailRequest $request
     * @return JsonResponse
     */
    public function getProductDetail(ProductDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductFacade::getProductDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش کالا
     * @param ProductEditRequest $request
     * @return JsonResponse
     */
    public function editProduct(ProductEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductFacade::editProduct($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن کالا
     * @param ProductAddRequest $request
     * @return JsonResponse
     */
    public function addProduct(ProductAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductFacade::addProduct($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف کالا
     * @param ProductDetailRequest $request
     * @return JsonResponse
     */
    public function deleteProduct(ProductDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductFacade::deleteProduct($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
