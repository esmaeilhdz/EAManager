<?php

namespace App\Http\Controllers;

use App\Facades\PlaceFacade;
use App\Http\Requests\Place\PlaceAddRequest;
use App\Http\Requests\Place\PlaceDetailRequest;
use App\Http\Requests\Place\PlaceEditRequest;
use App\Http\Requests\Place\PlaceListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class PlaceController extends Controller
{
    use Common;

    /**
     * سرویس لیست مکان ها
     * @param PlaceListRequest $request
     * @return JsonResponse
     */
    public function getPlaces(PlaceListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PlaceFacade::getPlaces($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات مکان
     * @param PlaceDetailRequest $request
     * @return JsonResponse
     */
    public function getPlaceDetail(PlaceDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PlaceFacade::getPlaceDetail($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش مکان
     * @param PlaceEditRequest $request
     * @return JsonResponse
     */
    public function editPlace(PlaceEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PlaceFacade::editPlace($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن مکان
     * @param PlaceAddRequest $request
     * @return JsonResponse
     */
    public function addPlace(PlaceAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PlaceFacade::addPlace($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف مکان
     * @param PlaceDetailRequest $request
     * @return JsonResponse
     */
    public function deletePlace(PlaceDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PlaceFacade::deletePlace($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
