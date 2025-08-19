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
        // Allow preview mode to bypass authentication completely
        if ($request->boolean('preview', false)) {
            return $next($request);
        }

        // Start PHP session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check PHP session first (primary method)
        $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
        $isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';

        // Fallback to Laravel session if PHP session not found
        if (!$isLoggedIn) {
            $isLoggedIn = session('logged_in') && session('user_id');
            $isAdmin = session('user_role') === 'admin';
        }

        // Check if user is logged in
        if (!$isLoggedIn) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Check if user is an admin
        if (!$isAdmin) {
            return redirect()->route('student.dashboard')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
