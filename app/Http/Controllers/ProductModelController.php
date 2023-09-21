<?php

namespace App\Http\Controllers;

use App\Facades\ProductModelFacade;
use App\Http\Requests\ProductModel\ProductModelAddRequest;
use App\Http\Requests\ProductModel\ProductModelComboRequest;
use App\Http\Requests\ProductModel\ProductModelDetailRequest;
use App\Http\Requests\ProductModel\ProductModelEditRequest;
use App\Http\Requests\ProductModel\ProductModelListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ProductModelController extends Controller
{
    use Common;

    /**
     * سرویس لیست مدل های کالا
     * @param ProductModelListRequest $request
     * @return JsonResponse
     */
    public function getProductModels(ProductModelListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductModelFacade::getProductModels($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات مدل کالا
     * @param ProductModelDetailRequest $request
     * @return JsonResponse
     */
    public function getProductModelDetail(ProductModelDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = ProductModelFacade::getProductModelDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس کامبوی مدل کالا
     * @param ProductModelComboRequest $request
     * @return JsonResponse
     */
    public function getProductModelCombo(ProductModelComboRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = ProductModelFacade::getProductModelCombo($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش مدل کالا
     * @param ProductModelEditRequest $request
     * @return JsonResponse
     */
    public function editProductModel(ProductModelEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductModelFacade::editProductModel($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن مدل کالا
     * @param ProductModelAddRequest $request
     * @return JsonResponse
     */
    public function addProductModel(ProductModelAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductModelFacade::addProductModel($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف مدل کالا
     * @param ProductModelDetailRequest $request
     * @return JsonResponse
     */
    public function deleteProductModel(ProductModelDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ProductModelFacade::deleteProductModel($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
