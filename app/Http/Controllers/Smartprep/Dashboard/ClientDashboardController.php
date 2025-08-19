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
            // Determine user role (default to student for clients)
            $userRole = auth()->user()->role ?? 'student';
            
            // Map client role to student if needed
            if ($userRole === 'client') {
                $userRole = 'student';
            }

            $sidebarColors = SettingsHelper::getSidebarColors($userRole);
            
            return response()->json([
                'success' => true,
                'role' => $userRole,
                'colors' => $sidebarColors
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
