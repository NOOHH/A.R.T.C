<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class CsrfTokenFix
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Ensure session is started
        if (!Session::isStarted()) {
            Session::start();
        }

        // Regenerate CSRF token if it doesn't exist
        if (!$request->session()->has('_token')) {
            $request->session()->regenerateToken();
        }

        // Set secure cookie parameters for HTTPS
        if ($request->secure()) {
            Config::set('session.secure', true);
            Config::set('session.same_site', 'lax');
            Config::set('session.http_only', true);
        }

        return $next($request);
    }
}
