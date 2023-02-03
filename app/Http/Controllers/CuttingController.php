<?php

namespace App\Http\Controllers;

use App\Facades\CuttingFacade;
use App\Http\Requests\Cutting\CuttingAddRequest;
use App\Http\Requests\Cutting\CuttingDetailRequest;
use App\Http\Requests\Cutting\CuttingEditRequest;
use App\Http\Requests\Cutting\CuttingListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class CuttingController extends Controller
{
    use Common;

    /**
     * سرویس لیست برش ها
     * @param CuttingListRequest $request
     * @return JsonResponse
     */
    public function getCuttings(CuttingListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CuttingFacade::getCuttings($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data'], $result['other']);
    }

    /**
     * سرویس جزئیات برش
     * @param CuttingDetailRequest $request
     * @return JsonResponse
     */
    public function getCuttingDetail(CuttingDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CuttingFacade::getCuttingDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش برش
     * @param CuttingEditRequest $request
     * @return JsonResponse
     */
    public function editCutting(CuttingEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CuttingFacade::editCutting($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن برش
     * @param CuttingAddRequest $request
     * @return JsonResponse
     */
    public function addCutting(CuttingAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CuttingFacade::addCutting($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف برش
     * @param CuttingDetailRequest $request
     * @return JsonResponse
     */
    public function deleteCutting(CuttingDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = CuttingFacade::deleteCutting($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
