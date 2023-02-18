<?php

namespace App\Repositories\Interfaces;

interface iAttachment
{
    public function getAttachments($inputs);

    public function getAttachmentDetail($inputs, $select = [], $relation = []);

    public function addAttachment($inputs, $file_upload_result, $user);

    public function deleteAttachment($attachment);
}
