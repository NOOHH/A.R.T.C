<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Utf8EncodingMiddleware
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
        // Set proper UTF-8 encoding for the request
        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding('UTF-8');
        }
        
        if (function_exists('mb_http_output')) {
            mb_http_output('UTF-8');
        }
        
        // Set default charset
        ini_set('default_charset', 'UTF-8');
        
        $response = $next($request);
        
        // Ensure response has proper UTF-8 headers
        if (!$response->headers->has('Content-Type')) {
            $response->header('Content-Type', 'text/html; charset=UTF-8');
        }
        
        return $response;
    }
}
