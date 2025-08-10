<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

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
            config(['session.secure' => true]);
            config(['session.same_site' => 'lax']);
        }

        return $next($request);
    }
}
