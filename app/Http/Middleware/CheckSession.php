<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\SessionManager;

class CheckSession
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
        // Check if this is a preview request
        if ($request->boolean('preview', false)) {
            return $next($request);
        }
        
        // Initialize session
        SessionManager::init();

        // Skip session check for public routes
        $publicRoutes = ['/', '/login', '/register'];
        if (in_array($request->path(), $publicRoutes)) {
            return $next($request);
        }

        // Check if user is logged in
        if (!SessionManager::isLoggedIn()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized', 'redirect' => '/'], 401);
            }
            return redirect('/')->with('error', 'Please log in to access this page.');
        }

        // Get user type and current route
        $userType = SessionManager::getUserType();
        $route = $request->path();

        // Define route prefixes for different user types
        $restrictedRoutes = [
            'student' => ['admin', 'professor'],
            'professor' => ['admin', 'student'],
            'admin' => ['student']
        ];

        // Check if user is trying to access restricted area
        if (isset($restrictedRoutes[$userType])) {
            foreach ($restrictedRoutes[$userType] as $restricted) {
                if (str_starts_with($route, $restricted)) {
                    return redirect("/{$userType}/dashboard")
                        ->with('error', 'You do not have permission to access that area.');
                }
            }
        }

        // Add user data to view
        $userData = [
            'user_id' => SessionManager::get('user_id'),
            'user_type' => SessionManager::get('user_type'),
            'user_name' => SessionManager::get('user_name'),
            'user_firstname' => SessionManager::get('user_firstname'),
            'user_lastname' => SessionManager::get('user_lastname'),
            'user_email' => SessionManager::get('user_email')
        ];

        view()->share('sessionUser', $userData);

        return $next($request);
    }
}
