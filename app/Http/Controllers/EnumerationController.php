<?php

namespace App\Http\Controllers;

use App\Facades\EnumerationFacade;
use App\Http\Requests\Enumeration\EnumerationAddRequest;
use App\Http\Requests\Enumeration\EnumerationDeleteRequest;
use App\Http\Requests\Enumeration\EnumerationEditRequest;
use App\Http\Requests\Enumeration\EnumerationGroupRequest;
use App\Http\Requests\Enumeration\EnumerationListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class EnumerationController extends Controller
{
    use Common;

    /**
    * سرویس لیست مقادیر
     * @param EnumerationListRequest $request
     * @return JsonResponse
     */
    public function getEnumerations(EnumerationListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = EnumerationFacade::getEnumerations($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس گروه مقادیر
     * @param EnumerationGroupRequest $request
     * @return JsonResponse
     */
    public function getEnumerationGrouped(EnumerationGroupRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = EnumerationFacade::getEnumerationGrouped($inputs['category_name']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش مقادیر
     * @param EnumerationEditRequest $request
     * @return JsonResponse
     */
    public function editEnumeration(EnumerationEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = EnumerationFacade::editEnumeration($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن مقادیر
     * @param EnumerationAddRequest $request
     * @return JsonResponse
     */
    public function addEnumeration(EnumerationAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = EnumerationFacade::addEnumeration($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف مقادیر
     * @param EnumerationDeleteRequest $request
     * @return JsonResponse
     */
    public function deleteEnumeration(EnumerationDeleteRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = EnumerationFacade::deleteEnumeration($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
