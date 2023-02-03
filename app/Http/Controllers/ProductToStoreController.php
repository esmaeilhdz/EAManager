<?php

namespace App\Http\Controllers;

use App\Facades\ProductToStoreFacade;
use App\Http\Requests\ProductToStore\ProductToStoreAddRequest;
use App\Http\Requests\ProductToStore\ProductToStoreDetailRequest;
use App\Http\Requests\ProductToStore\ProductToStoreEditRequest;
use App\Http\Requests\ProductToStore\ProductToStoreListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ProductToStoreController extends Controller
{
    use Common;

    /**
     * سرویس لیست ارسال کالا به فروشگاه
     * @param ProductToStoreListRequest $request
     * @return JsonResponse
     */
    public function getProductToStores(ProductToStoreListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductToStoreFacade::getProductToStores($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data'], $result['other']);
    }

    /**
     * سرویس جزئیات ارسال کالا به فروشگاه
     * @param ProductToStoreDetailRequest $request
     * @return JsonResponse
     */
    public function getProductToStoreDetail(ProductToStoreDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductToStoreFacade::getProductToStoreDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش ارسال کالا به فروشگاه
     * @param ProductToStoreEditRequest $request
     * @return JsonResponse
     */
    public function editProductToStore(ProductToStoreEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductToStoreFacade::editProductToStore($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن ارسال کالا به فروشگاه
     * @param ProductToStoreAddRequest $request
     * @return JsonResponse
     */
    public function addProductToStore(ProductToStoreAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductToStoreFacade::addProductToStore($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف ارسال کالا به فروشگاه
     * @param ProductToStoreDetailRequest $request
     * @return JsonResponse
     */
    public function deleteProductToStore(ProductToStoreDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductToStoreFacade::deleteProductToStore($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
