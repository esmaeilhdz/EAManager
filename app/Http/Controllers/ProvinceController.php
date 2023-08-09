<?php

namespace App\Http\Controllers;

use App\Facades\ProvinceFacade;
use Illuminate\Http\JsonResponse;

class ProvinceController extends Controller
{
    public function getProvinces(): JsonResponse
    {
        $result = ProvinceFacade::getProvinces();
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
