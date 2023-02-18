<?php

namespace App\Traits;

trait AttachmentTrait
{

    public function getFileType($ext): ?string
    {
        $image_ext = ['jpg', 'png', 'jpeg'];
        $document_ext = ['doc', 'docx', 'xls', 'xlsx'];
        $media_ext = ['mp3', 'mp4'];
        $type = null;

        if (in_array($ext, $image_ext)) {
            $type = 'original';
        } elseif (in_array($ext, $document_ext)) {
            $type = 'document';
        } elseif (in_array($ext, $media_ext)) {
            $type = 'media';
        }

        return $type;
    }
}
