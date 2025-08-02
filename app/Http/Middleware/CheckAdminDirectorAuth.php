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

        // Check if user is logged in
        if (!$isLoggedIn) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Check if user is an admin or director
        if (!$isAdmin && !$isDirector) {
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
