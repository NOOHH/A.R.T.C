<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class ForceHttps
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
        if (App::environment('production')) {
            // Force HTTPS for all requests
            if (!$request->secure()) {
                return redirect()->secure($request->getRequestUri());
            }
            
            // Configure session for HTTPS
            Config::set('session.secure', true);
            Config::set('session.same_site', 'lax');
            Config::set('session.http_only', true);
            
            // Set cookie domain if not already set
            if (!Config::get('session.domain')) {
                Config::set('session.domain', '.laravel-zfurp.sevalla.app');
            }
            
            // Ensure CSRF token is properly configured
            Config::set('session.cookie', 'laravel_session');
            Config::set('session.lifetime', 120);
        }
        
        return $next($request);
    }
}
