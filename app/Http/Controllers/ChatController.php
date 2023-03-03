<?php

namespace App\Http\Controllers;

use App\Facades\ChatFacade;
use App\Http\Requests\Chat\ChatDetailRequest;
use App\Http\Requests\Chat\ChatListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    use Common;

    /**
     * سرویس لیست چت ها
     * @param ChatListRequest $request
     * @return JsonResponse
     */
    public function getChats(ChatListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ChatFacade::getChats($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس جزئیات چت
     * @param ChatDetailRequest $request
     * @return JsonResponse
     */
    public function getChatDetail(ChatDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = ChatFacade::getChatDetail($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
