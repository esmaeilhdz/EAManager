<?php

namespace App\Http\Controllers;

use App\Facades\GroupConversationFacade;
use App\Http\Requests\ChatGroup\ChatGroupAddRequest;
use App\Http\Requests\ChatGroup\ChatGroupDetailRequest;
use App\Http\Requests\ChatGroup\ChatGroupEditRequest;
use App\Http\Requests\ChatGroup\ChatGroupListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class GroupConversationController extends Controller
{
    use Common;

    /**
     * سرویس لیست گروه های چت
     * @param ChatGroupListRequest $request
     * @return JsonResponse
     */
    public function getGroupConversations(ChatGroupListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = GroupConversationFacade::getGroupConversations($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات گروه چت
     * @param ChatGroupDetailRequest $request
     * @return JsonResponse
     */
    public function getGroupConversationDetail(ChatGroupDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = GroupConversationFacade::getGroupConversationDetail($inputs['id']);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس ویرایش گروه چت
     * @param ChatGroupEditRequest $request
     * @return JsonResponse
     */
    public function editGroupConversation(ChatGroupEditRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = GroupConversationFacade::editGroupConversation($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن گروه چت
     * @param ChatGroupAddRequest $request
     * @return JsonResponse
     */
    public function addGroupConversation(ChatGroupAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = GroupConversationFacade::addGroupConversation($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف گروه چت
     * @param ChatGroupDetailRequest $request
     * @return JsonResponse
     */
    public function deleteGroupConversation(ChatGroupDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = GroupConversationFacade::deleteGroupConversation($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
