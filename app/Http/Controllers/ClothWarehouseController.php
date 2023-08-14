<?php

namespace App\Http\Controllers;

use App\Facades\ClothWarehouseFacade;
use App\Http\Requests\ClothWarehouse\ClothWarehouseListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ClothWarehouseController extends Controller
{
    use Common;

    /**
     * سرویس لیست انبارهای پارچه
     * @param ClothWarehouseListRequest $request
     * @return JsonResponse
     */
    public function getClothWarehouses(ClothWarehouseListRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $result = ClothWarehouseFacade::getClothWarehouses($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data'], $result['other']);
    }
}
