<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DebugController extends Controller
{
    public function authDebug(Request $request)
    {
        $debug = [
            'timestamp' => now(),
            'request_info' => [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'php_session' => [
                'session_id' => session_id(),
                'session_status' => session_status(),
                'session_data' => $_SESSION ?? null,
            ],
            'laravel_session' => [
                'all' => $request->session()->all(),
                'user_id' => session('user_id'),
                'user_type' => session('user_type'),
                'user_role' => session('user_role'),
                'user_name' => session('user_name'),
                'logged_in' => session('logged_in'),
            ],
            'auth_guards' => [
                'default' => Auth::user(),
                'director' => Auth::guard('director')->user(),
                'admin' => Auth::guard('admin')->user() ?? 'Guard not configured',
            ],
            'middleware_simulation' => $this->simulateMiddleware($request),
            'csrf_token' => [
                'from_session' => $request->session()->token(),
                'from_meta' => $request->header('X-CSRF-TOKEN'),
            ],
        ];

        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    }

    private function simulateMiddleware($request)
    {
        // Start PHP session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check PHP session first (primary method)
        $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
        $userType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
        $isAdmin = $userType === 'admin';
        $isDirector = $userType === 'director';

        $phpSessionCheck = [
            'is_logged_in' => $isLoggedIn,
            'user_type' => $userType,
            'is_admin' => $isAdmin,
            'is_director' => $isDirector,
        ];

        // Fallback to Laravel session if PHP session not found
        if (!$isLoggedIn) {
            $isLoggedIn = session('logged_in') && session('user_id');
            $userType = session('user_role');
            $isAdmin = $userType === 'admin';
            $isDirector = $userType === 'director';
        }

        $laravelSessionCheck = [
            'is_logged_in' => session('logged_in') && session('user_id'),
            'user_type' => session('user_role'),
            'is_admin' => session('user_role') === 'admin',
            'is_director' => session('user_role') === 'director',
        ];

        $finalDecision = [
            'would_pass' => $isLoggedIn && ($isAdmin || $isDirector),
            'final_logged_in' => $isLoggedIn,
            'final_is_admin' => $isAdmin,
            'final_is_director' => $isDirector,
        ];

        return [
            'php_session_check' => $phpSessionCheck,
            'laravel_session_check' => $laravelSessionCheck,
            'final_decision' => $finalDecision,
        ];
    }
}
