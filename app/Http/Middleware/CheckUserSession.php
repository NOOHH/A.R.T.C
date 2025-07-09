<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserSession
{
    /**
     * Handle an incoming request.
     * Check if user is logged in via session, redirect to welcome page if not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user has valid session
        if (!session()->has('user_id') || !session()->has('user_role') || !session('logged_in')) {
            // Clear any partial session data
            session()->flush();
            
            // Redirect to welcome page (homepage)
            return redirect()->route('welcome')->with('error', 'Please log in to access this page.');
        }

        return $next($request);
    }
}
