<?php

namespace App;

use Illuminate\Http\JsonResponse;

class ApiResponse
{

    /**
     * @param $status_code
     * @param $message
     * @param $data
     * @param array $other
     * @return JsonResponse
     */
    public function response($status_code, $message, $data, array $other = []): JsonResponse
    {
        if (!is_numeric($status_code)) {
            if ($status_code) {
                $status_code = 200;
            } else {
                $status_code = 400;
            }
        }
        return response()->json([
            'status' => $status_code,
            'message' => $message,
            'data' => $data,
            'other' => $other,
        ], $status_code);
    }
}
