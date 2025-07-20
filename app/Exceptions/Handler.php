<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Always return JSON for AJAX, API, or admin requests
        if (
            $request->ajax() ||
            $request->wantsJson() ||
            $request->is('api/*') ||
            $request->is('registration/*') ||
            $request->is('admin/*')
        ) {
            // Handle validation exceptions with proper status and error structure
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $exception->errors(),
                ], 422);
            }
            // Use Symfony HttpExceptionInterface for status code if available
            $status = 500;
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $status = $exception->getStatusCode();
            }
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'error' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ], $status);
        }
        return parent::render($request, $exception);
    }
}
