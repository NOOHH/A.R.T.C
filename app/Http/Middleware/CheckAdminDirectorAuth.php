<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        // Log debug information
        Log::info('CheckAdminDirectorAuth middleware triggered', [
            'url' => $request->url(),
            'method' => $request->method(),
            'php_session' => $_SESSION ?? [],
            'laravel_session' => session()->all()
        ]);

        // Check PHP session first (primary method)
        $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
        $userType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
        $isAdmin = $userType === 'admin';
        $isDirector = $userType === 'director';

        // Fallback to Laravel session if PHP session not found
        if (!$isLoggedIn) {
            $isLoggedIn = session('logged_in') && session('user_id');
            $userType = session('user_role');
            $isAdmin = $userType === 'admin';
            $isDirector = $userType === 'director';
        }

        // Additional fallback: check if Laravel Auth has a user
        $authUser = Auth::user();
        $authAdminUser = Auth::guard('admin')->user();
        $authDirectorUser = Auth::guard('director')->user();
        
        if (!$isLoggedIn && ($authUser || $authAdminUser || $authDirectorUser)) {
            $isLoggedIn = true;
            if ($authAdminUser) {
                $isAdmin = true;
                $userType = 'admin';
            } elseif ($authDirectorUser) {
                $isDirector = true;
                $userType = 'director';
            }
        }

        Log::info('Authentication check result', [
            'isLoggedIn' => $isLoggedIn,
            'userType' => $userType,
            'isAdmin' => $isAdmin,
            'isDirector' => $isDirector,
            'auth_user' => $authUser ? $authUser->id : null,
            'auth_admin' => $authAdminUser ? $authAdminUser->admin_id : null,
            'auth_director' => $authDirectorUser ? $authDirectorUser->director_id : null
        ]);

        // Check if user is logged in
        if (!$isLoggedIn) {
            Log::warning('Access denied: User not logged in');
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Check if user is an admin or director
        if (!$isAdmin && !$isDirector) {
            Log::warning('Access denied: User not admin or director', ['userType' => $userType]);
            return redirect()->route('student.dashboard')->with('error', 'Access denied. Admin or Director privileges required.');
        }

        // If director, check feature flag
        if ($isDirector) {
            $path = $request->path();
            $featureMap = [
                'admin/programs' => 'director_manage_programs',
                'admin/professors' => 'director_manage_professors',
                'admin/batches' => 'director_manage_batches',
                'admin/settings' => 'director_manage_settings',
                'admin/enrollments' => 'director_manage_enrollments',
                'admin/analytics' => 'director_view_analytics',
                'admin/modules' => 'director_manage_modules',
                'admin/students' => 'director_view_students',
                'admin/announcements' => 'director_manage_announcements',
                'admin/quiz-generator' => 'director_manage_modules', // Use modules permission for quiz generator
            ];
            foreach ($featureMap as $prefix => $settingKey) {
                if (str_starts_with($path, $prefix)) {
                    $canAccess = \App\Models\AdminSetting::getValue($settingKey, 'false') === 'true' || \App\Models\AdminSetting::getValue($settingKey, '0') === '1';
                    if (!$canAccess) {
                        abort(403, 'Access denied: You do not have permission to access this section.');
                    }
                }
            }
        }

        return $next($request);
    }
}
