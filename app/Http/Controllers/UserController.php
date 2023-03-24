<?php

namespace App\Http\Controllers;

use App\Facades\UserFacade;
use App\Http\Requests\User\UserAddRequest;
use App\Http\Requests\User\UserDetailRequest;
use App\Http\Requests\User\UserEditRequest;
use App\Http\Requests\User\UserListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use Common;

    /**
     * سرویس لیست کاربران
     * @param UserListRequest $request
     * @return JsonResponse
     */
    public function getUsers(UserListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = UserFacade::getUsers($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات کاربر
     * @param UserDetailRequest $request
     * @return JsonResponse
     */
    public function getUserDetail(UserDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = UserFacade::getUserDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function getUserInfo()
    {
        $result = UserFacade::getUserInfo();
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش کاربر
     * @param UserEditRequest $request
     * @return JsonResponse
     */
    public function editUser(UserEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = UserFacade::editUser($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن کاربر
     * @param UserAddRequest $request
     * @return JsonResponse
     */
    public function addUser(UserAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = UserFacade::addUser($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف کاربر
     * @param UserDetailRequest $request
     * @return JsonResponse
     */
    public function deleteUser(UserDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = UserFacade::deleteUser($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
