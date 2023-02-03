<?php

namespace App\Http\Controllers;

use App\Facades\SewingFacade;
use App\Http\Requests\Sewing\SewingAddRequest;
use App\Http\Requests\Sewing\SewingDetailRequest;
use App\Http\Requests\Sewing\SewingEditRequest;
use App\Http\Requests\Sewing\SewingListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class SewingController extends Controller
{
    use Common;

    /**
     * سرویس لیست دوخت ها
     * @param SewingListRequest $request
     * @return JsonResponse
     */
    public function getSewings(SewingListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SewingFacade::getSewings($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات دوخت
     * @param SewingDetailRequest $request
     * @return JsonResponse
     */
    public function getSewingDetail(SewingDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SewingFacade::getSewingDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش دوخت
     * @param SewingEditRequest $request
     * @return JsonResponse
     */
    public function editSewing(SewingEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SewingFacade::editSewing($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن دوخت
     * @param SewingAddRequest $request
     * @return JsonResponse
     */
    public function addSewing(SewingAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SewingFacade::addSewing($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف دوخت
     * @param SewingDetailRequest $request
     * @return JsonResponse
     */
    public function deleteSewing(SewingDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SewingFacade::deleteSewing($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
