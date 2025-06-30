<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in via session
        if (!session('logged_in') || !session('user_id')) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Check if user is an admin
        if (session('user_role') !== 'admin') {
            return redirect()->route('student.dashboard')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
