<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\FileManager;
use App\Repositories\Interfaces\iAttachment;
use App\Traits\AttachmentTrait;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class AttachmentHelper
{
    use Common, AttachmentTrait;

    // attributes
    public iAttachment $attachment_interface;

    public function __construct(iAttachment $attachment_interface)
    {
        $this->attachment_interface = $attachment_interface;
    }

    /**
     * لیست پیوست ها
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function getAttachments($inputs): array
    {
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        $inputs['order_by'] = $this->orderBy($inputs, 'attachments');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $accessories = $this->attachment_interface->getAttachments($inputs);

        $accessories->transform(function ($item) {
            return [
                'code' => $item->code,
                'attachment_type' => [
                    'id' => $item->attachment_type_id,
                    'caption' => $item->attachment_type->enum_caption
                ],
                'address' => env('APP_URL') . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . $item->path . DIRECTORY_SEPARATOR . $item->file_name . '.' . $item->ext,
                'type' => $item->type,
                'creator' => is_null($item->creator->person) ? null : [
                    'person' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $accessories
        ];
    }

    /**
     * افزودن پیوست
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function addAttachment($inputs): array
    {
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);
        $file = request()->file('file');
        $real_path = $file->getRealPath();
        $ext = $file->extension();
        $file_type = $this->getFileType($ext);
        $imgFile = Image::make($real_path);

        $file_manager = new FileManager($file, $imgFile);
        $upload_result = $file_manager->uploadFile($inputs, $real_path, $file_type);
        if (!$upload_result) {
            return [
                'result' => false,
                'message' => __('messages.file_upload_failed'),
                'data' => null
            ];
        }

        $user = Auth::user();
        DB::beginTransaction();
        $result = [];
        $parent_id = 0;
        foreach ($upload_result as $item) {
            $item['parent_id'] = $parent_id;
            $res = $this->attachment_interface->addAttachment($inputs, $item, $user);
            if ($item['type'] == 'original') {
                $parent_id = $res['data'];
            }
            $result[] = $res['result'];
        }

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * حذف پیوست
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function deleteAttachment($inputs): array
    {
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);
        $attachment = $this->attachment_interface->getAttachmentDetail($inputs, ['id', 'parent_id']);
        if (is_null($attachment)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->attachment_interface->deleteAttachment($attachment);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
