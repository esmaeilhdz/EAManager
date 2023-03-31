<?php

namespace App\Http\Controllers;

use App\Facades\PermissionFacade;
use App\Http\Requests\Permission\EditRolePermissionRequest;
use App\Http\Requests\Permission\GetRolePermissionRequest;
use App\Traits\Common;

class PermissionController extends Controller
{
    use Common;

    public function getRolePermissions(GetRolePermissionRequest $request)
    {
        $inputs = $request->validated();

        $result = PermissionFacade::getRolePermissions($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    public function editRolePermissions(EditRolePermissionRequest $request)
    {
        $inputs = $request->validated();

        $result = PermissionFacade::editRolePermissions($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
