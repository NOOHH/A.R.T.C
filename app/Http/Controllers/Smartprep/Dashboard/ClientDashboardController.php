<?php

namespace App\Http\Controllers\Smartprep\Dashboard;

use App\Http\Controllers\Controller;
use App\Helpers\SettingsHelper;
use Illuminate\Http\JsonResponse;

class ClientDashboardController extends Controller
{
    public function index()
    {
        return view('smartprep.dashboard.client', [
            'activeWebsites' => collect([]),
            'websiteRequests' => collect([])
        ]);
    }

    /**
     * Get sidebar settings for the current user's role
     */
    public function getSidebarSettings(): JsonResponse
    {
        try {
            // Determine user role from multiple sources
            $userRole = 'student'; // default
            
            // Check Laravel Auth first
            if (auth()->check()) {
                $userRole = auth()->user()->role ?? 'student';
            }
            
            // Check session data (common in this app)
            if (session('user_role')) {
                $userRole = session('user_role');
            }
            
            // Check for professor-specific session
            if (session('professor_id') || session('user_type') === 'professor') {
                $userRole = 'professor';
            }
            
            // Map client role to student if needed
            if ($userRole === 'client') {
                $userRole = 'student';
            }
            
            // Validate role
            if (!in_array($userRole, ['student', 'professor', 'admin'])) {
                $userRole = 'student';
            }

            $sidebarColors = SettingsHelper::getSidebarColors($userRole);
            
            return response()->json([
                'success' => true,
                'role' => $userRole,
                'colors' => $sidebarColors,
                'debug' => [
                    'auth_user_role' => auth()->check() ? (auth()->user()->role ?? 'none') : 'not_logged_in',
                    'session_user_role' => session('user_role'),
                    'session_professor_id' => session('professor_id'),
                    'final_role' => $userRole
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading sidebar settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
