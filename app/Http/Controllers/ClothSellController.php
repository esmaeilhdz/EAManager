<?php

namespace App\Http\Controllers;

use App\Facades\ClothSellFacade;
use App\Http\Requests\ClothSell\ClothSellAddRequest;
use App\Http\Requests\ClothSell\ClothSellDetailRequest;
use App\Http\Requests\ClothSell\ClothSellEditRequest;
use App\Http\Requests\ClothSell\ClothSellListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ClothSellController extends Controller
{
    use Common;

    /**
     * سرویس لیست فروشهای پارچه
     * @param ClothSellListRequest $request
     * @return JsonResponse
     */
    public function getClothSells(ClothSellListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothSellFacade::getClothSells($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات فروش پارچه
     * @param ClothSellDetailRequest $request
     * @return JsonResponse
     */
    public function getClothSellDetail(ClothSellDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothSellFacade::getClothSellDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش فروش پارچه
     * @param ClothSellEditRequest $request
     * @return JsonResponse
     */
    public function editClothSell(ClothSellEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothSellFacade::editClothSell($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن فروش پارچه
     * @param ClothSellAddRequest $request
     * @return JsonResponse
     */
    public function addClothSell(ClothSellAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothSellFacade::addClothSell($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف فروش پارچه
     * @param ClothSellDetailRequest $request
     * @return JsonResponse
     */
    public function deleteClothSell(ClothSellDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothSellFacade::deleteClothSell($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
