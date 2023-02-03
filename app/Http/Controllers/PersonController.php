<?php

namespace App\Http\Controllers;

use App\Facades\PersonFacade;
use App\Facades\PlaceFacade;
use App\Http\Requests\Person\PersonAddRequest;
use App\Http\Requests\Person\PersonEditRequest;
use App\Http\Requests\Person\PersonListRequest;
use App\Http\Requests\Person\PersonDetailRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class PersonController extends Controller
{
    use Common;

    /**
     * سرویس لیست افراد
     * @param PersonListRequest $request
     * @return JsonResponse
     */
    public function getPersons(PersonListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PersonFacade::getPersons($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات فرد
     * @param PersonDetailRequest $request
     * @return JsonResponse
     */
    public function getPersonDetail(PersonDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PersonFacade::getPersonDetail($inputs['code']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش فرد
     * @param PersonEditRequest $request
     * @return JsonResponse
     */
    public function editPerson(PersonEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PersonFacade::editPerson($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن فرد
     * @param PersonAddRequest $request
     * @return JsonResponse
     */
    public function addPerson(PersonAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PersonFacade::addPerson($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف فرد
     * @param PersonDetailRequest $request
     * @return JsonResponse
     */
    public function deletePerson(PersonDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = PersonFacade::deletePerson($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
