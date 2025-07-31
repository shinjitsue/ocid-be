<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $exception)
    {
        // Return a JSON response for 404 errors
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'Resource not found.'
            ], 404);
        }

        return parent::render($request, $exception);
    }
}