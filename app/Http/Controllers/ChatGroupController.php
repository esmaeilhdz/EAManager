<?php

namespace App\Http\Controllers;

use App\Facades\ChatGroupFacade;
use App\Http\Requests\ChatGroup\ChatGroupAddRequest;
use App\Http\Requests\ChatGroup\ChatGroupDetailRequest;
use App\Http\Requests\ChatGroup\ChatGroupEditRequest;
use App\Http\Requests\ChatGroup\ChatGroupListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ChatGroupController extends Controller
{
    use Common;

    /**
     * سرویس لیست گروه های چت
     * @param ChatGroupListRequest $request
     * @return JsonResponse
     */
    public function getChatGroups(ChatGroupListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ChatGroupFacade::getChatGroups($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات گروه چت
     * @param ChatGroupDetailRequest $request
     * @return JsonResponse
     */
    public function getChatGroupDetail(ChatGroupDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ChatGroupFacade::getChatGroupDetail($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش گروه چت
     * @param ChatGroupEditRequest $request
     * @return JsonResponse
     */
    public function editChatGroup(ChatGroupEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ChatGroupFacade::editChatGroup($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن گروه چت
     * @param ChatGroupAddRequest $request
     * @return JsonResponse
     */
    public function addChatGroup(ChatGroupAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ChatGroupFacade::addChatGroup($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف گروه چت
     * @param ChatGroupDetailRequest $request
     * @return JsonResponse
     */
    public function deleteChatGroup(ChatGroupDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ChatGroupFacade::deleteChatGroup($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
