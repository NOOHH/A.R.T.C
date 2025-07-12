<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDirectorAuth
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
        // Start PHP session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check PHP session first (primary method)
        $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
        $isDirector = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'director';

        // Fallback to Laravel session if PHP session not found
        if (!$isLoggedIn) {
            $isLoggedIn = session('logged_in') && session('user_id');
            $isDirector = session('user_role') === 'director';
        }

        // Check if user is logged in
        if (!$isLoggedIn) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Check if user is a director
        if (!$isDirector) {
            return redirect()->route('student.dashboard')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
