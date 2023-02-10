<?php

namespace App\Http\Controllers;

use App\Facades\InvoiceFacade;
use App\Http\Requests\Invoice\InvoiceAddRequest;
use App\Http\Requests\Invoice\InvoiceDetailRequest;
use App\Http\Requests\Invoice\InvoiceEditRequest;
use App\Http\Requests\Invoice\InvoiceListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    use Common;

    /**
     * سرویس لیست پیش فاکتور ها
     * @param InvoiceListRequest $request
     * @return JsonResponse
     */
    public function getInvoices(InvoiceListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = InvoiceFacade::getInvoices($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات پیش فاکتور
     * @param InvoiceDetailRequest $request
     * @return JsonResponse
     */
    public function getInvoiceDetail(InvoiceDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = InvoiceFacade::getInvoiceDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش پیش فاکتور
     * @param InvoiceEditRequest $request
     * @return JsonResponse
     */
    public function editInvoice(InvoiceEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = InvoiceFacade::editInvoice($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن پیش فاکتور
     * @param InvoiceAddRequest $request
     * @return JsonResponse
     */
    public function addInvoice(InvoiceAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = InvoiceFacade::addInvoice($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف پیش فاکتور
     * @param InvoiceDetailRequest $request
     * @return JsonResponse
     */
    public function deleteInvoice(InvoiceDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = InvoiceFacade::deleteInvoice($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
