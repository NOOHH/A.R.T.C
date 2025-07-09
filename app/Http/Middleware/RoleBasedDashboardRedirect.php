<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleBasedDashboardRedirect
{
    /**
     * Handle an incoming request.
     * Ensure users access only their correct dashboard based on role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (!session()->has('user_role')) {
            return redirect()->route('welcome')->with('error', 'Please log in to access this page.');
        }

        $userRole = session('user_role');
        $currentRoute = $request->route()->getName();

        // Define dashboard routes for each role
        $roleDashboards = [
            'student' => 'student.dashboard',
            'professor' => 'professor.dashboard', 
            'admin' => 'admin.dashboard',
            'director' => 'admin.dashboard' // Directors use admin dashboard
        ];

        // Check if user is accessing wrong dashboard
        if ($this->isWrongDashboard($currentRoute, $userRole, $roleDashboards)) {
            // Redirect to correct dashboard
            $correctDashboard = $roleDashboards[$userRole] ?? 'welcome';
            return redirect()->route($correctDashboard)
                ->with('warning', 'You have been redirected to your appropriate dashboard.');
        }

        return $next($request);
    }

    /**
     * Check if user is accessing wrong dashboard
     */
    private function isWrongDashboard($currentRoute, $userRole, $roleDashboards)
    {
        // Get all dashboard routes
        $allDashboardRoutes = array_values($roleDashboards);
        
        // If current route is a dashboard route
        if (in_array($currentRoute, $allDashboardRoutes)) {
            // Check if it's the correct dashboard for user's role
            $correctDashboard = $roleDashboards[$userRole] ?? null;
            return $currentRoute !== $correctDashboard;
        }
        
        // Also check for dashboard-related routes (like student.*, admin.*, professor.*)
        foreach ($roleDashboards as $role => $dashboardRoute) {
            if ($role !== $userRole && $this->isRoleRoute($currentRoute, $role)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if route belongs to a specific role
     */
    private function isRoleRoute($route, $role)
    {
        // Check if route starts with role prefix
        return strpos($route, $role . '.') === 0;
    }
}
