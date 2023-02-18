<?php

namespace App\Http\Controllers;

use App\Facades\AttachmentFacade;
use App\Http\Requests\Attachment\AttachmentAddRequest;
use App\Http\Requests\Attachment\AttachmentDetailRequest;
use App\Http\Requests\Attachment\AttachmentEditRequest;
use App\Http\Requests\Attachment\AttachmentListRequest;
use App\Traits\Common;
use Illuminate\Http\JsonResponse;

class AttachmentController extends Controller
{
    use Common;

    /**
     * سرویس لیست پیوست ها
     * @param AttachmentListRequest $request
     * @return JsonResponse
     */
    public function getAttachments(AttachmentListRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AttachmentFacade::getAttachments($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس افزودن پیوست
     * @param AttachmentAddRequest $request
     * @return JsonResponse
     */
    public function addAttachment(AttachmentAddRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AttachmentFacade::addAttachment($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }

    /**
     * سرویس حذف پیوست
     * @param AttachmentDetailRequest $request
     * @return JsonResponse
     */
    public function deleteAttachment(AttachmentDetailRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $this->cleanInput($inputs, array_keys($request->rules()));

        $result = AttachmentFacade::deleteAttachment($inputs);
        return $this->api_response->response($result['result'], $result['message'], $result['data']);
    }
}
