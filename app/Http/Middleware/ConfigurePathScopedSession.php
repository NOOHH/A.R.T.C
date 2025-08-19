<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConfigurePathScopedSession
{
    /**
     * Ensure SmartPrep routes use a separate session cookie and path so
     * actions inside preview iframes (ARTC) don't log the SmartPrep user out.
     */
    public function handle(Request $request, Closure $next)
    {
        // Apply only for SmartPrep-prefixed routes
        if ($request->is('smartprep') || $request->is('smartprep/*')) {
            // Use a distinct cookie name and scope it to /smartprep
            config([
                'session.cookie' => 'smartprep_session',
                'session.path'   => '/smartprep',
                // Ensure cookie matches the current host (avoid 'localhost' vs '127.0.0.1' issues)
                'session.domain' => null,
                // Respect scheme
                'session.secure' => $request->isSecure(),
                // Lax is fine for login redirects
                'session.same_site' => 'lax',
            ]);
        }

        return $next($request);
    }
}


