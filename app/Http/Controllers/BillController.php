<?php

namespace App\Http\Controllers;

use App\Facades\BillFacade;
use App\Http\Requests\Bill\BillAddRequest;
use App\Http\Requests\Bill\BillDetailRequest;
use App\Http\Requests\Bill\BillEditRequest;
use App\Http\Requests\Bill\BillListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class BillController extends Controller
{use Common;

    /**
     * سرویس لیست قبض ها
     * @param BillListRequest $request
     * @return JsonResponse
     */
    public function getBills(BillListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = BillFacade::getBills($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات قبض
     * @param BillDetailRequest $request
     * @return JsonResponse
     */
    public function getBillDetail(BillDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = BillFacade::getBillDetail($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش قبض
     * @param BillEditRequest $request
     * @return JsonResponse
     */
    public function editBill(BillEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = BillFacade::editBill($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن قبض
     * @param BillAddRequest $request
     * @return JsonResponse
     */
    public function addBill(BillAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = BillFacade::addBill($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف قبض
     * @param BillDetailRequest $request
     * @return JsonResponse
     */
    public function deleteBill(BillDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = BillFacade::deleteBill($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
