<?php

namespace App\Http\Controllers;

use App\Facades\ChatGroupPersonFacade;
use App\Http\Requests\ChatGroupPerson\ChatGroupPersonAddRequest;
use App\Http\Requests\ChatGroupPerson\ChatGroupPersonDetailRequest;
use App\Http\Requests\ChatGroupPerson\ChatGroupPersonEditRequest;
use App\Http\Requests\ChatGroupPerson\ChatGroupPersonListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ChatGroupPersonController extends Controller
{
    use Common;

    /**
     * سرویس لیست افراد گروه های چت
     * @param ChatGroupPersonListRequest $request
     * @return JsonResponse
     */
    public function getChatGroupPersons(ChatGroupPersonListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ChatGroupPersonFacade::getChatGroupPersons($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data'], $result['other']);
    }

    /**
     * سرویس جزئیات فرد گروه چت
     * @param ChatGroupPersonDetailRequest $request
     * @return JsonResponse
     */
    public function getChatGroupPersonDetail(ChatGroupPersonDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ChatGroupPersonFacade::getChatGroupPersonDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن فرد گروه چت
     * @param ChatGroupPersonAddRequest $request
     * @return JsonResponse
     */
    public function addChatGroupPerson(ChatGroupPersonAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ChatGroupPersonFacade::addChatGroupPerson($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف فرد گروه چت
     * @param ChatGroupPersonDetailRequest $request
     * @return JsonResponse
     */
    public function deleteChatGroupPerson(ChatGroupPersonDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ChatGroupPersonFacade::deleteChatGroupPerson($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
