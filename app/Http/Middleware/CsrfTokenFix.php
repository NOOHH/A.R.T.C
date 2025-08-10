<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

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

        // Only try to access session if it's available
        try {
            if ($request->hasSession()) {
                // Regenerate CSRF token if it doesn't exist
                if (!$request->session()->has('_token')) {
                    $request->session()->regenerateToken();
                    Log::info('CSRF Token regenerated', [
                        'session_id' => $request->session()->getId(),
                        'token' => $request->session()->token(),
                        'url' => $request->url()
                    ]);
                }
                
                // Log CSRF token for debugging
                if ($request->is('login') || $request->is('*/login')) {
                    Log::info('Login page CSRF token', [
                        'session_id' => $request->session()->getId(),
                        'csrf_token' => $request->session()->token(),
                        'secure' => $request->secure(),
                        'domain' => config('session.domain')
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Session not available yet, continue without error
            Log::warning('Session not available in CsrfTokenFix', [
                'error' => $e->getMessage(),
                'url' => $request->url()
            ]);
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
