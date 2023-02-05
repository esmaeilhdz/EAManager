<?php

namespace App\Http\Controllers;

use App\Facades\AddressFacade;
use App\Http\Requests\Address\AddressAddRequest;
use App\Http\Requests\Address\AddressDetailRequest;
use App\Http\Requests\Address\AddressEditRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    use Common;

    /**
     * سرویس ویرایش آدرس
     * @param AddressEditRequest $request
     * @return JsonResponse
     */
    public function editAddress(AddressEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AddressFacade::editAddress($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن آدرس
     * @param AddressAddRequest $request
     * @return JsonResponse
     */
    public function addAddress(AddressAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AddressFacade::addAddress($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف آدرس
     * @param AddressDetailRequest $request
     * @return JsonResponse
     */
    public function deleteAddress(AddressDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AddressFacade::deleteAddress($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
