<?php

namespace App\Http\Controllers;

use App\Facades\RoleFacade;
use App\Http\Requests\Role\RoleAddRequest;
use App\Http\Requests\Role\RoleDetailRequest;
use App\Http\Requests\Role\RoleEditRequest;
use App\Http\Requests\Role\RoleListRequest;
use App\Http\Requests\Role\RoleTreeRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    use Common;

    /**
     * سرویس لیست نقش ها
     * @param RoleListRequest $request
     * @return JsonResponse
     */
    public function getRoles(RoleListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = RoleFacade::getRoles($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس نقش ها برای نمایش در درخت
     * @param RoleTreeRequest $request
     * @return JsonResponse
     */
    public function getRolesTree(RoleTreeRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = RoleFacade::getRolesTree($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات نقش
     * @param RoleDetailRequest $request
     * @return JsonResponse
     */
    public function getRoleDetail(RoleDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = RoleFacade::getRoleDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش نقش
     * @param RoleEditRequest $request
     * @return JsonResponse
     */
    public function editRole(RoleEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = RoleFacade::editRole($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن نقش
     * @param RoleAddRequest $request
     * @return JsonResponse
     */
    public function addRole(RoleAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = RoleFacade::addRole($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
