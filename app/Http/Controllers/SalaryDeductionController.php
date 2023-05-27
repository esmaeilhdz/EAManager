<?php

namespace App\Http\Controllers;

use App\Facades\SalaryDeductionFacade;
use App\Http\Requests\SalaryDeduction\SalaryDeductionAddRequest;
use App\Http\Requests\SalaryDeduction\SalaryDeductionDetailRequest;
use App\Http\Requests\SalaryDeduction\SalaryDeductionEditRequest;
use App\Http\Requests\SalaryDeduction\SalaryDeductionListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class SalaryDeductionController extends Controller
{
    use Common;

    /**
     * سرویس لیست کسورات حقوق
     * @param SalaryDeductionListRequest $request
     * @return JsonResponse
     */
    public function getSalaryDeductions(SalaryDeductionListRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = SalaryDeductionFacade::getSalaryDeductions($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data'], $result['other']);
    }

    /**
     * سرویس کسر حقوق
     * @param SalaryDeductionDetailRequest $request
     * @return JsonResponse
     */
    public function getSalaryDeductionDetail(SalaryDeductionDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = SalaryDeductionFacade::getSalaryDeductionDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش کسر حقوق
     * @param SalaryDeductionEditRequest $request
     * @return JsonResponse
     */
    public function editSalaryDeduction(SalaryDeductionEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalaryDeductionFacade::editSalaryDeduction($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن کسر حقوق
     * @param SalaryDeductionAddRequest $request
     * @return JsonResponse
     */
    public function addSalaryDeduction(SalaryDeductionAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = SalaryDeductionFacade::addSalaryDeduction($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف کسر حقوق
     * @param SalaryDeductionDetailRequest $request
     * @return JsonResponse
     */
    public function deleteSalaryDeduction(SalaryDeductionDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = SalaryDeductionFacade::deleteSalaryDeduction($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
