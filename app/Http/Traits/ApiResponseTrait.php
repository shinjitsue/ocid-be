<?php

namespace App\Http\Traits;

trait ApiResponseTrait
{
    protected function successResponse($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse(string $message = 'Error', int $code = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    protected function validationErrorResponse(array $errors, string $message = 'Validation failed', int $code = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    protected function notFoundResponse(string $message = 'Resource not found', int $code = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $code);
    }

    protected function unauthorizedResponse(string $message = 'Unauthorized', int $code = 401)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => null,
        ], $code);
    }
}
