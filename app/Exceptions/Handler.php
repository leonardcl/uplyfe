<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Force JSON responses for /api/* — never leak Ignition HTML.
        $this->renderable(function (Throwable $e, $request) {
            if (!$request->is('api/*')) {
                return null;
            }
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            $status = method_exists($e, 'getStatusCode')
                ? $e->getStatusCode()
                : (in_array($e->getCode(), [400, 401, 403, 404, 405, 409, 422, 429, 500, 502, 503], true) ? $e->getCode() : 500);
            $payload = ['error' => $e->getMessage() ?: 'Server error'];
            if (config('app.debug')) {
                $payload['exception'] = class_basename($e);
            }
            return response()->json($payload, $status);
        });
    }
}
