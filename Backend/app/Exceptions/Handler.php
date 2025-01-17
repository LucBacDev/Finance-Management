<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
{
    if ($exception instanceof \Illuminate\Validation\ValidationException) {
        return response()->json([
            'success' => false,
            'errors' => $exception->errors(), // Trả về các lỗi cụ thể
        ], 422);
    }

    return parent::render($request, $exception);
}

}
