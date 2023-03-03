<?php

namespace App\Http\Controllers;

use App\Facades\ReportFacade;
use App\Http\Requests\Report\ReportListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    use Common;

    /**
     * سرویس لیست مشتریان بدهکار
     * @param ReportListRequest $request
     * @return JsonResponse
     */
    public function getDebtorCustomers(ReportListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ReportFacade::getDebtorCustomers($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

}
