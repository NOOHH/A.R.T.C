<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminDirectorAuth
{
    /**
     * Handle an incoming request.
     * Allow access for both admin and director roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Start PHP session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check PHP session first (primary method)
        $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
        $isAdminOrDirector = isset($_SESSION['user_type']) && in_array($_SESSION['user_type'], ['admin', 'director']);

        // Fallback to Laravel session if PHP session not found
        if (!$isLoggedIn) {
            $isLoggedIn = session('logged_in') && session('user_id');
            $isAdminOrDirector = in_array(session('user_role'), ['admin', 'director']);
        }

        // Check if user is logged in
        if (!$isLoggedIn) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Check if user is an admin or director
        if (!$isAdminOrDirector) {
            return redirect()->route('student.dashboard')->with('error', 'Access denied. Admin or Director privileges required.');
        }

        return $next($request);
    }
}
