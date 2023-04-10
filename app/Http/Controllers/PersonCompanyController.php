<?php

namespace App\Http\Controllers;

use App\Facades\PersonCompanyFacade;
use App\Http\Requests\PersonCompany\PersonCompanyDetailRequest;
use App\Http\Requests\PersonCompany\PersonCompanyEditRequest;
use App\Http\Requests\PersonCompany\PersonCompanyListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class PersonCompanyController extends Controller
{
    use Common;

    /**
     * سرویس لیست شرکت های یک فرد
     * @param PersonCompanyListRequest $request
     * @return JsonResponse
     */
    public function getCompaniesOfPerson(PersonCompanyListRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = PersonCompanyFacade::getCompaniesOfPerson($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات ارتباط شرکت و شخص
     * @param PersonCompanyDetailRequest $request
     * @return JsonResponse
     */
    public function getCompanyOfPerson(PersonCompanyDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = PersonCompanyFacade::getCompanyOfPerson($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش خرید خرج کار
     * @param PersonCompanyEditRequest $request
     * @return JsonResponse
     */
    public function editAccessoryBuy(PersonCompanyEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PersonCompanyFacade::editAccessoryBuy($inputs);
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

        $result = PersonCompanyFacade::addAccessoryBuy($inputs);
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

        $result = PersonCompanyFacade::deleteAccessoryBuy($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
