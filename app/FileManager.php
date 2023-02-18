<?php

namespace App;

use Intervention\Image\Facades\Image;

class FileManager
{
    private $file;
    private string $real_path;
    private $imgFile;

    public function __construct($file, $imgFile)
    {
        $this->file = $file;
        $this->real_path = $file->getRealPath();
        $this->imgFile = $imgFile;
    }

    private function getUploadPath(): array
    {
        $date = date('Y-m-d');
        list($year, $month, $day) = explode('-', $date);

        if (!is_dir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $year))) {
            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $year));
        }

        if (!is_dir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month))) {
            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month));
        }

        if (!is_dir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day))) {
            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day));
        }

        return [
            storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day),
            $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day
        ];

    }

    private function generateNewFileName(): string
    {
        return md5(microtime());
    }

    private function uploadThumbnail($return, $ext, $upload_path, $db_path, $original_file_name)
    {
        $file_name = $this->generateNewFileName();

        $result = $this->imgFile->resize(150, 225, function ($constraint) {
            $constraint->aspectRatio();
        })->save("$upload_path/$file_name.$ext");

        $return[] = [
            'path' => $db_path,
            'file_name' => $file_name,
            'original_file_name' => $original_file_name,
            'ext' => $ext,
            'type' => 'thumb'
        ];

        return $return;
    }

    private function uploadAvatar($return, $ext, $upload_path, $db_path, $original_file_name)
    {
        $file_name = $this->generateNewFileName();

        $result = $this->imgFile->resize(80, 80, function ($constraint) {
            $constraint->aspectRatio();
        })->save("$upload_path/$file_name.$ext");

        $return[] = [
            'path' => $db_path,
            'file_name' => $file_name,
            'original_file_name' => $original_file_name,
            'ext' => $ext,
            'type' => 'avatar'
        ];

        return $return;
    }


    public function uploadFile($inputs, $real_path, $type): bool|array
    {
        $ext = $this->file->extension();

        list($upload_path, $db_path) = $this->getUploadPath();
        $file_name = $this->generateNewFileName();
        $original_file_name = $this->file->getClientOriginalName();

        $result = $this->file->move($upload_path, "$file_name.$ext");

        $return[] = [
            'path' => $db_path,
            'file_name' => $file_name,
            'original_file_name' => $original_file_name,
            'ext' => $ext,
            'type' => $type
        ];

        if (is_file($result)) {
            // فایل آپلودی از نوع عکس است.
            // باید thumbnail هم تولید شود.
            if ($type == 'original') {
                $return = $this->uploadThumbnail($return, $ext, $upload_path, $db_path, $original_file_name);

                // عکس پرسنلی است.
                // باید آواتار هم تولید شود.
                if ($inputs['attachment_type_id'] == 1) {
                    $return = $this->uploadAvatar($return, $ext, $upload_path, $db_path, $original_file_name);
                }
            }

            return $return;
        } else {
            return false;
        }
    }
}
