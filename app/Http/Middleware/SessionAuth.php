<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SessionAuth
{
    /**
     * Handle an incoming request for session-based authentication
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check various session authentication methods
        $isAuthenticated = false;
        $userId = null;
        $userRole = null;

        // Check Laravel session
        if (session('logged_in') && session('user_id')) {
            $isAuthenticated = true;
            $userId = session('user_id');
            $userRole = session('user_role') ?? session('role');
        }

        // Check PHP native session for admin
        if (!$isAuthenticated && isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
            $isAuthenticated = true;
            $userId = $_SESSION['admin_id'] ?? null;
            $userRole = 'admin';
        }

        // Check other session formats
        if (!$isAuthenticated) {
            // Student session check
            if (session('student_logged_in') && session('student_id')) {
                $isAuthenticated = true;
                $userId = session('student_id');
                $userRole = 'student';
            }
            
            // Professor session check
            if (session('logged_in') && session('professor_id')) {
                $isAuthenticated = true;
                $userId = session('professor_id');
                $userRole = 'professor';
            }

            // Director session check
            if (session('logged_in') && session('director_id')) {
                $isAuthenticated = true;
                $userId = session('director_id');
                $userRole = 'director';
            }
        }

        if (!$isAuthenticated || !$userId) {
            // For AJAX/API requests, return JSON error
            if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Not authenticated',
                    'message' => 'Please log in to access this resource',
                    'debug' => [
                        'session_user_id' => session('user_id'),
                        'session_logged_in' => session('logged_in'),
                        'session_role' => session('user_role') ?? session('role'),
                        'php_session_admin' => $_SESSION['admin_logged_in'] ?? false,
                        'request_path' => $request->path()
                    ]
                ], 401);
            }
            
            // For regular requests, redirect to appropriate login
            if ($request->is('admin/*')) {
                return redirect()->route('admin.login')->with('error', 'Please log in as admin');
            } elseif ($request->is('professor/*')) {
                return redirect()->route('professor.login')->with('error', 'Please log in as professor');
            } elseif ($request->is('director/*')) {
                return redirect()->route('director.login')->with('error', 'Please log in as director');
            } else {
                return redirect()->route('student.login')->with('error', 'Please log in');
            }
        }

        // Store authenticated user info in request for easy access
        $request->merge([
            'auth_user_id' => $userId,
            'auth_user_role' => $userRole
        ]);

        // Set up Laravel Auth user for compatibility
        $this->setAuthUser($userId, $userRole);

        return $next($request);
    }

    /**
     * Set up Laravel Auth user from session data
     */
    private function setAuthUser($userId, $userRole)
    {
        try {
            // Create a generic user object for Auth::user()
            $authUser = new \App\Models\User();
            
            // Set basic properties
            $authUser->user_id = $userId;
            $authUser->role = $userRole;
            
            // Get additional user data based on role
            switch ($userRole) {
                case 'student':
                    $userData = \App\Models\User::find($userId);
                    if ($userData) {
                        $authUser->user_firstname = $userData->user_firstname;
                        $authUser->user_lastname = $userData->user_lastname;
                        $authUser->email = $userData->email;
                        $authUser->exists = true;
                    }
                    break;
                    
                case 'professor':
                    $professorData = \App\Models\Professor::find($userId);
                    if ($professorData) {
                        $authUser->user_firstname = $professorData->professor_first_name ?? $professorData->professor_name;
                        $authUser->user_lastname = $professorData->professor_last_name ?? '';
                        $authUser->email = $professorData->professor_email;
                        $authUser->exists = true;
                    }
                    break;
                    
                case 'admin':
                    $adminData = \App\Models\Admin::find($userId);
                    if ($adminData) {
                        $authUser->user_firstname = $adminData->admin_name;
                        $authUser->user_lastname = '';
                        $authUser->email = $adminData->email;
                        $authUser->exists = true;
                    }
                    break;
                    
                case 'director':
                    $directorData = \App\Models\Director::find($userId);
                    if ($directorData) {
                        $authUser->user_firstname = $directorData->directors_first_name ?? $directorData->directors_name;
                        $authUser->user_lastname = $directorData->directors_last_name ?? '';
                        $authUser->email = $directorData->directors_email;
                        $authUser->exists = true;
                    }
                    break;
            }
            
            // Set the authenticated user in Laravel's Auth system
            \Illuminate\Support\Facades\Auth::setUser($authUser);
            
        } catch (\Exception $e) {
            Log::warning('Failed to set Auth user: ' . $e->getMessage());
        }
    }
}
