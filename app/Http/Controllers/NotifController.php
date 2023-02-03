<?php

namespace App\Http\Controllers;

use App\Facades\NotifFacade;
use App\Http\Requests\Notif\NotifAddRequest;
use App\Http\Requests\Notif\NotifDetailRequest;
use App\Http\Requests\Notif\NotifEditRequest;
use App\Http\Requests\Notif\NotifListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class NotifController extends Controller
{
    use Common;

    /**
     * سرویس لیست اعلان ها
     * @param NotifListRequest $request
     * @return JsonResponse
     */
    public function getNotifs(NotifListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = NotifFacade::getNotifs($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات اعلان
     * @param NotifDetailRequest $request
     * @return JsonResponse
     */
    public function getNotifDetail(NotifDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = NotifFacade::getNotifDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش اعلان
     * @param NotifEditRequest $request
     * @return JsonResponse
     */
    public function editNotif(NotifEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = NotifFacade::editNotif($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن اعلان
     * @param NotifAddRequest $request
     * @return JsonResponse
     */
    public function addNotif(NotifAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = NotifFacade::addNotif($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف اعلان
     * @param NotifDetailRequest $request
     * @return JsonResponse
     */
    public function deleteNotif(NotifDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = NotifFacade::deleteNotif($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
