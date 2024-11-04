<?php

namespace App\Helper;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function make(string $status = 'success', array $data = [], ?string $message = null, array $extra = [], int $code = 200): JsonResponse
    {
        return response()->json(
            [
                'status' => $status,
                'data' => $data,
                'message' => $message,
                ...$extra
            ],
            $code
        );
    }
}
