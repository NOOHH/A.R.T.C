<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestAuthController extends Controller
{
    public function checkAuth(Request $request)
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
            'user_id' => $_SESSION['user_id'] ?? null,
            'user_name' => $_SESSION['user_name'] ?? null,
        ];

        // Fallback to Laravel session if PHP session not found
        $laravelLoggedIn = false;
        $laravelUserType = null;
        $laravelIsAdmin = false;
        $laravelIsDirector = false;
        
        if (!$isLoggedIn) {
            $laravelLoggedIn = session('logged_in') && session('user_id');
            $laravelUserType = session('user_role');
            $laravelIsAdmin = $laravelUserType === 'admin';
            $laravelIsDirector = $laravelUserType === 'director';
            
            // Update final values if Laravel session is valid
            $isLoggedIn = $laravelLoggedIn;
            $userType = $laravelUserType;
            $isAdmin = $laravelIsAdmin;
            $isDirector = $laravelIsDirector;
        }

        $laravelSessionCheck = [
            'is_logged_in' => $laravelLoggedIn,
            'user_type' => $laravelUserType,
            'is_admin' => $laravelIsAdmin,
            'is_director' => $laravelIsDirector,
            'user_id' => session('user_id'),
            'user_name' => session('user_name'),
        ];

        $finalDecision = [
            'would_pass_middleware' => $isLoggedIn && ($isAdmin || $isDirector),
            'final_logged_in' => $isLoggedIn,
            'final_is_admin' => $isAdmin,
            'final_is_director' => $isDirector,
            'final_user_type' => $userType,
        ];

        $response = [
            'timestamp' => now(),
            'middleware_check' => 'CheckAdminDirectorAuth simulation',
            'php_session' => $phpSessionCheck,
            'laravel_session' => $laravelSessionCheck,
            'final_decision' => $finalDecision,
            'session_data' => [
                'php_session_all' => $_SESSION ?? null,
                'laravel_session_all' => session()->all(),
            ],
            'request_info' => [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'csrf_token' => $request->header('X-CSRF-TOKEN'),
                'user_agent' => $request->userAgent(),
            ]
        ];

        Log::info('Admin Auth Check Test', $response);

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }
    
    public function testSave(Request $request)
    {
        // This is a test endpoint to simulate the quiz save without actually saving
        Log::info('=== TEST QUIZ SAVE REQUEST ===', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'input' => $request->all(),
            'session' => session()->all(),
            'php_session' => $_SESSION ?? null,
        ]);
        
        // Check admin auth like the real controller does
        $adminId = session('user_id');
        if (!$adminId) {
            Log::error('TEST: Admin not found for saving quiz', [
                'session_user_id' => session('user_id'),
                'session_user_type' => session('user_type'),
                'all_session' => session()->all()
            ]);
            return response()->json(['success' => false, 'message' => 'Admin session not found.'], 401);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'TEST: Would save quiz successfully',
            'admin_id' => $adminId,
            'test_data' => $request->all()
        ]);
    }
}
