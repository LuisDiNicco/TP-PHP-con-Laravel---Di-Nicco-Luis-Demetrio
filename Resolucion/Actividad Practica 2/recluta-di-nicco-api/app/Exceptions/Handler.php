<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    protected $levels = [];
    protected $dontReport = [];
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        //
    }

    public function render($request, Throwable $exception)
    {
        // Si es una ruta API
        if ($request->is('api/*')) {

            // Validaciones
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'Los datos enviados son inválidos.',
                    'errors' => $exception->errors(),
                ], 422);
            }

            // Errores HTTP (404, 403, etc.)
            if ($exception instanceof HttpException) {
                return response()->json([
                    'message' => $exception->getMessage() ?: 'Error en la petición',
                ], $exception->getStatusCode());
            }

            // Cualquier otra excepción → 500 genérico
            return response()->json([
                'message' => 'Ocurrió un error interno en el servidor.',
            ], 500);
        }

        // Para web, dejar comportamiento normal
        return parent::render($request, $exception);
    }
}
