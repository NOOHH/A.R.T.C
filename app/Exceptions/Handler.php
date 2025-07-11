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
        // Force JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->is('api/*') || $request->is('registration/*')) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'error' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
