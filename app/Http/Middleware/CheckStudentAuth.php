<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckStudentAuth
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

        // Check if user is a student
        if (session('user_role') !== 'student') {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
