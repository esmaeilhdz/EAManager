<?php

namespace App\Http\Controllers;

use App\Facades\AccessoryBuyFacade;
use App\Http\Requests\AccessoryBuy\AccessoryBuyAddRequest;
use App\Http\Requests\AccessoryBuy\AccessoryBuyDetailRequest;
use App\Http\Requests\AccessoryBuy\AccessoryBuyEditRequest;
use App\Http\Requests\AccessoryBuy\AccessoryBuyListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class AccessoryBuyController extends Controller
{
    use Common;

    /**
     * سرویس لیست خرید خرج کار ها
     * @param AccessoryBuyListRequest $request
     * @return JsonResponse
     */
    public function getAccessoryBuys(AccessoryBuyListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccessoryBuyFacade::getAccessoryBuys($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات خرید خرج کار
     * @param AccessoryBuyDetailRequest $request
     * @return JsonResponse
     */
    public function getAccessoryBuyDetail(AccessoryBuyDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccessoryBuyFacade::getAccessoryBuyDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش خرید خرج کار
     * @param AccessoryBuyEditRequest $request
     * @return JsonResponse
     */
    public function editAccessoryBuy(AccessoryBuyEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccessoryBuyFacade::editAccessoryBuy($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن خرید خرج کار
     * @param AccessoryBuyAddRequest $request
     * @return JsonResponse
     */
    public function addAccessoryBuy(AccessoryBuyAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccessoryBuyFacade::addAccessoryBuy($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف خرید خرج کار
     * @param AccessoryBuyDetailRequest $request
     * @return JsonResponse
     */
    public function deleteAccessoryBuy(AccessoryBuyDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccessoryBuyFacade::deleteAccessoryBuy($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
