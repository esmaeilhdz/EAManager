<?php

namespace App\Http\Controllers;

use App\Facades\MenuFacade;

class MenuController extends Controller
{
    /**
     * منو
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenu()
    {
        $result = MenuFacade::getMenu();
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
