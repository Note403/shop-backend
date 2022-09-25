<?php

namespace App\Service;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class Response
{
    public static function success($data = null): JsonResponse
    {
        return response()->json([
            'success' =>  true,
            'error' => false,
            'data' => $data
        ]);
    }

    public static function error(string $error_code): JsonResponse
    {
        return response()->json([
            'success' => true,
            'error' => $error_code,
            'data' => null
        ]);
    }

    public static function json(Collection|array $data): JsonResponse
    {
        return response()->json([
            'success' => true,
            'error' => false,
            'data' => $data
        ]);
    }
}
