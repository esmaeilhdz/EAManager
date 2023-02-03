<?php

namespace App\Http\Controllers;

use App\Facades\ClothBuyFacade;
use App\Http\Requests\ClothBuy\ClothBuyAddRequest;
use App\Http\Requests\ClothBuy\ClothBuyDetailRequest;
use App\Http\Requests\ClothBuy\ClothBuyEditRequest;
use App\Http\Requests\ClothBuy\ClothBuyListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ClothBuyController extends Controller
{
    use Common;

    /**
     * سرویس لیست خریدهای پارچه
     * @param ClothBuyListRequest $request
     * @return JsonResponse
     */
    public function getClothBuys(ClothBuyListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothBuyFacade::getClothBuys($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات خرید پارچه
     * @param ClothBuyDetailRequest $request
     * @return JsonResponse
     */
    public function getClothBuyDetail(ClothBuyDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothBuyFacade::getClothBuyDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش خرید پارچه
     * @param ClothBuyEditRequest $request
     * @return JsonResponse
     */
    public function editClothBuy(ClothBuyEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothBuyFacade::editClothBuy($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن خرید پارچه
     * @param ClothBuyAddRequest $request
     * @return JsonResponse
     */
    public function addClothBuy(ClothBuyAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothBuyFacade::addClothBuy($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف خرید پارچه
     * @param ClothBuyDetailRequest $request
     * @return JsonResponse
     */
    public function deleteClothBuy(ClothBuyDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothBuyFacade::deleteClothBuy($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
