<?php

namespace App\Http\Controllers;

use App\Facades\SalaryFacade;
use App\Http\Requests\Salary\SalaryAddRequest;
use App\Http\Requests\Salary\SalaryAllListRequest;
use App\Http\Requests\Salary\SalaryDetailRequest;
use App\Http\Requests\Salary\SalaryEditRequest;
use App\Http\Requests\Salary\SalaryListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class SalaryController extends Controller
{
    use Common;

    /**
     * سرویس لیست حقوق های افراد
     * @param SalaryAllListRequest $request
     * @return JsonResponse
     */
    public function getAllSalaries(SalaryAllListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalaryFacade::getAllSalaries($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس لیست حقوق های یک فرد
     * @param SalaryListRequest $request
     * @return JsonResponse
     */
    public function getSalaries(SalaryListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalaryFacade::getSalaries($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات فرد
     * @param SalaryDetailRequest $request
     * @return JsonResponse
     */
    public function getSalaryDetail(SalaryDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalaryFacade::getSalaryDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش فرد
     * @param SalaryEditRequest $request
     * @return JsonResponse
     */
    public function editSalary(SalaryEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalaryFacade::editSalary($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن فرد
     * @param SalaryAddRequest $request
     * @return JsonResponse
     */
    public function addSalary(SalaryAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalaryFacade::addSalary($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
