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
        '/logout', // Exclude main logout route from CSRF verification
        '/student/logout', // Exclude student logout route from CSRF verification
        '/professor/logout', // Exclude professor logout route from CSRF verification
        '/enrollment/logout', // Exclude enrollment logout route from CSRF verification
        // Professor preview routes - bypass CSRF for preview mode
        't/*/professor/meetings/*/start',
        't/*/professor/meetings/*/finish',
        't/*/professor/meetings/*/stats',
        't/draft/*/professor/meetings/*/start',
        't/draft/*/professor/meetings/*/finish',
        't/draft/*/professor/meetings/*/stats',
    ];
}
