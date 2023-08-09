<?php

namespace App\Http\Controllers;

use App\Facades\CityFacade;
use App\Http\Requests\City\getCitiesRequest;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    public function getCities(getCitiesRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = CityFacade::getCities($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
