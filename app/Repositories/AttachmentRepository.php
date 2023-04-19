<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Attachment;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AttachmentRepository implements Interfaces\iAttachment
{
    use Common;

    /**
     * لیست پیوست ها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getAttachments($inputs): LengthAwarePaginator
    {
        try {
            return Attachment::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'attachment_type:enum_id,enum_caption'
            ])
                ->select([
                    'code',
                    'attachment_type_id',
                    'path',
                    'file_name',
                    'ext',
                    'type',
                    'created_by',
                    'created_at'
                ])
                ->where('model_type', $inputs['model_type'])
                ->where('model_id', $inputs['model_id'])
                ->where('type', 'thumb')
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات پیوست
     * @param $inputs
     * @param array $select
     * @param array $relation
     * @return Builder|Builder[]|Collection|Model|null
     * @throws ApiException
     */
    public function getAttachmentDetail($inputs, $select = [], $relation = []): Model|Collection|Builder|array|null
    {
        try {
            $attachment = Attachment::where('model_type', $inputs['model_type'])
                ->where('model_id', $inputs['model_id'])
                ->where('code', $inputs['code']);

            if (count($select)) {
                $attachment = $attachment->select($select);
            }

            if (count($relation)) {
                $attachment = $attachment->with($relation);
            }

            return $attachment->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن پیوست
     * @param $inputs
     * @param $file_upload_result
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addAttachment($inputs, $file_upload_result, $user): array
    {
        try {
            $attachment = new Attachment();

            $attachment->code = $this->randomString();
            $attachment->model_type = $inputs['model_type'];
            $attachment->model_id = $inputs['model_id'];
            $attachment->parent_id = $file_upload_result['parent_id'] > 0 ? $file_upload_result['parent_id'] : null;
            $attachment->attachment_type_id = $inputs['attachment_type_id'];
            $attachment->path = $file_upload_result['path'];
            $attachment->file_name = $file_upload_result['file_name'];
            $attachment->ext = $file_upload_result['ext'];
            $attachment->original_file_name = $file_upload_result['original_file_name'];
            $attachment->type = $file_upload_result['type'];
            $attachment->created_by = $user->id;

            return [
                'result' => $attachment->save(),
                'data' => [
                    'id' => $attachment->code ?? null,
                    'path' => env('APP_URL') . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . $attachment->path . DIRECTORY_SEPARATOR . $attachment->file_name . '.' . $attachment->ext,
                ]
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف پیوست
     * @param $attachment
     * @return mixed
     * @throws ApiException
     */
    public function deleteAttachment($attachment): mixed
    {
        try {
            // والد
            if (is_null($attachment->parent_id)) {
                return Attachment::where('parent_id', $attachment->id) // children
                    ->orWhere('id', $attachment->id) // self
                    ->delete();
            } else {
                return Attachment::where('id', $attachment->parent_id) // parent
                    ->orWhere('parent_id', $attachment->parent_id) // siblings and self
                    ->delete();
            }
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
