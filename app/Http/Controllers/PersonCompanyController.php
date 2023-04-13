<?php

namespace App\Http\Controllers;

use App\Facades\PersonCompanyFacade;
use App\Http\Requests\PersonCompany\PersonCompanyAddRequest;
use App\Http\Requests\PersonCompany\PersonCompanyChangeRequest;
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
     * سرویس ویرایش ارتباط شرکت و شخص
     * @param PersonCompanyEditRequest $request
     * @return JsonResponse
     */
    public function editPersonCompany(PersonCompanyEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PersonCompanyFacade::editPersonCompany($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش وضعیت ارتباط شرکت و شخص
     * @param PersonCompanyChangeRequest $request
     * @return JsonResponse
     */
    public function changePersonCompany(PersonCompanyChangeRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = PersonCompanyFacade::changePersonCompany($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن ارتباط شرکت و شخص
     * @param PersonCompanyAddRequest $request
     * @return JsonResponse
     */
    public function addPersonCompany(PersonCompanyAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PersonCompanyFacade::addPersonCompany($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف ارتباط شرکت و شخص
     * @param PersonCompanyDetailRequest $request
     * @return JsonResponse
     */
    public function deletePersonCompany(PersonCompanyDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = PersonCompanyFacade::deletePersonCompany($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
