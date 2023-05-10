<?php

namespace App\Http\Controllers;

use App\Facades\AccessoryFacade;
use App\Http\Requests\Accessory\AccessoryAddRequest;
use App\Http\Requests\Accessory\AccessoryDetailRequest;
use App\Http\Requests\Accessory\AccessoryEditRequest;
use App\Http\Requests\Accessory\AccessoryListRequest;
use App\Http\Requests\AccessoryEditStatusRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class AccessoryController extends Controller
{
    use Common;

    /**
     * سرویس لیست خرج کار ها
     * @param AccessoryListRequest $request
     * @return JsonResponse
     */
    public function getAccessories(AccessoryListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccessoryFacade::getAccessories($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات خرج کار
     * @param AccessoryDetailRequest $request
     * @return JsonResponse
     */
    public function getAccessoryDetail(AccessoryDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccessoryFacade::getAccessoryDetail($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش خرج کار
     * @param AccessoryEditRequest $request
     * @return JsonResponse
     */
    public function editAccessory(AccessoryEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccessoryFacade::editAccessory($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس تغییر وضعیت خرج کار
     * @param AccessoryEditStatusRequest $request
     * @return JsonResponse
     */
    public function changeStatusAccessory(AccessoryEditStatusRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = AccessoryFacade::changeStatusAccessory($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن خرج کار
     * @param AccessoryAddRequest $request
     * @return JsonResponse
     */
    public function addAccessory(AccessoryAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccessoryFacade::addAccessory($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف خرج کار
     * @param AccessoryDetailRequest $request
     * @return JsonResponse
     */
    public function deleteAccessory(AccessoryDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccessoryFacade::deleteAccessory($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
