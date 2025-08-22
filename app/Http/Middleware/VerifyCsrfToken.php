<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/admin/modules/course-content-store',
        'api/chat/*',
        'api/test-route',
        'api/debug-session',
        'api/me',
        '/enrollment/send-otp',
        '/enrollment/verify-otp',
        '/enrollment/validate-referral',
        '/enrollment/modular/submit',
        '/enrollment/modular/validate',
        '/registration/validate-file',
        '/modular/registration/validate-file',
        '/modular/registration/user-prefill',
        'smartprep/logout', // Temporarily exclude for testing
    ];
}
