<?php

namespace App\Http\Controllers;

use App\Facades\AccountFacade;
use App\Http\Requests\Account\AccountAddRequest;
use App\Http\Requests\Account\AccountDetailRequest;
use App\Http\Requests\Account\AccountEditRequest;
use App\Http\Requests\Account\AccountListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    use Common;

    /**
     * سرویس لیست حساب ها
     * @param AccountListRequest $request
     * @return JsonResponse
     */
    public function getAccounts(AccountListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccountFacade::getAccounts($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات حساب
     * @param AccountDetailRequest $request
     * @return JsonResponse
     */
    public function getAccountDetail(AccountDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccountFacade::getAccountDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش حساب
     * @param AccountEditRequest $request
     * @return JsonResponse
     */
    public function editAccount(AccountEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccountFacade::editAccount($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن حساب
     * @param AccountAddRequest $request
     * @return JsonResponse
     */
    public function addAccount(AccountAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccountFacade::addAccount($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف حساب
     * @param AccountDetailRequest $request
     * @return JsonResponse
     */
    public function deleteAccount(AccountDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AccountFacade::deleteAccount($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
