<?php

namespace App\Http\Controllers;

use App\Facades\CustomerFacade;
use App\Http\Requests\Customer\CustomerAddRequest;
use App\Http\Requests\Customer\CustomerComboRequest;
use App\Http\Requests\Customer\CustomerDetailRequest;
use App\Http\Requests\Customer\CustomerEditRequest;
use App\Http\Requests\Customer\CustomerListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    use Common;

    /**
     * سرویس لیست مشتری ها
     * @param CustomerListRequest $request
     * @return JsonResponse
     */
    public function getCustomers(CustomerListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CustomerFacade::getCustomers($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات مشتری
     * @param CustomerDetailRequest $request
     * @return JsonResponse
     */
    public function getCustomerDetail(CustomerDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CustomerFacade::getCustomerDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس کامبوی مشتری
     * @param CustomerComboRequest $request
     * @return JsonResponse
     */
    public function getCustomerCombo(CustomerComboRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CustomerFacade::getCustomerCombo($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش مشتری
     * @param CustomerEditRequest $request
     * @return JsonResponse
     */
    public function editCustomer(CustomerEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CustomerFacade::editCustomer($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن مشتری
     * @param CustomerAddRequest $request
     * @return JsonResponse
     */
    public function addCustomer(CustomerAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CustomerFacade::addCustomer($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف مشتری
     * @param CustomerDetailRequest $request
     * @return JsonResponse
     */
    public function deleteCustomer(CustomerDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CustomerFacade::deleteCustomer($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
