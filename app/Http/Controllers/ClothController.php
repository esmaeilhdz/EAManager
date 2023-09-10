<?php

namespace App\Http\Controllers;

use App\Facades\ClothFacade;
use App\Http\Requests\Cloth\ClothAddRequest;
use App\Http\Requests\Cloth\ClothComboRequest;
use App\Http\Requests\Cloth\ClothDetailRequest;
use App\Http\Requests\Cloth\ClothEditRequest;
use App\Http\Requests\Cloth\ClothListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ClothController extends Controller
{
    use Common;

    /**
     * سرویس لیست پارچه ها
     * @param ClothListRequest $request
     * @return JsonResponse
     */
    public function getClothes(ClothListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothFacade::getClothes($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات پارچه
     * @param ClothDetailRequest $request
     * @return JsonResponse
     */
    public function getClothDetail(ClothDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothFacade::getClothDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس کامبوی پارچه
     * @param ClothComboRequest $request
     * @return JsonResponse
     */
    public function getClothCombo(ClothComboRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothFacade::getClothCombo($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش پارچه
     * @param ClothEditRequest $request
     * @return JsonResponse
     */
    public function editCloth(ClothEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothFacade::editCloth($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن پارچه
     * @param ClothAddRequest $request
     * @return JsonResponse
     */
    public function addCloth(ClothAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothFacade::addCloth($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف پارچه
     * @param ClothDetailRequest $request
     * @return JsonResponse
     */
    public function deleteCloth(ClothDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ClothFacade::deleteCloth($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
