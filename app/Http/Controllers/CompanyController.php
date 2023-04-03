<?php

namespace App\Http\Controllers;

use App\Facades\CompanyFacade;
use App\Http\Requests\Company\CompanyAddRequest;
use App\Http\Requests\Company\CompanyComboRequest;
use App\Http\Requests\Company\CompanyDetailRequest;
use App\Http\Requests\Company\CompanyEditRequest;
use App\Http\Requests\Company\CompanyListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    use Common;

    /**
     * سرویس لیست شرکت ها
     * @param CompanyListRequest $request
     * @return JsonResponse
     */
    public function getCompanies(CompanyListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CompanyFacade::getCompanies($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function getCompanyCombo(CompanyComboRequest $request)
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CompanyFacade::getCompanyCombo($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات شرکت
     * @param CompanyDetailRequest $request
     * @return JsonResponse
     */
    public function getCompanyDetail(CompanyDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CompanyFacade::getCompanyDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش شرکت
     * @param CompanyEditRequest $request
     * @return JsonResponse
     */
    public function editCompany(CompanyEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CompanyFacade::editCompany($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن شرکت
     * @param CompanyAddRequest $request
     * @return JsonResponse
     */
    public function addCompany(CompanyAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CompanyFacade::addCompany($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف شرکت
     * @param CompanyDetailRequest $request
     * @return JsonResponse
     */
    public function deleteCompany(CompanyDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CompanyFacade::deleteCompany($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
