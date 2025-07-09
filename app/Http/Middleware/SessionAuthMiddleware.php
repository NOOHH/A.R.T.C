<?php

namespace App\Http\Middleware;

use App\Helpers\SessionManager;
use Closure;
use Illuminate\Http\Request;

class SessionAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        SessionManager::init();

        if (!SessionManager::isLoggedIn()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect('/');
        }

        // Check user type and redirect accordingly for dashboard access
        if ($request->is('admin/*') && SessionManager::getUserType() !== 'admin') {
            return redirect('/');
        }

        if ($request->is('professor/*') && SessionManager::getUserType() !== 'professor') {
            return redirect('/');
        }

        if ($request->is('student/*') && SessionManager::getUserType() !== 'student') {
            return redirect('/');
        }

        return $next($request);
    }
}
