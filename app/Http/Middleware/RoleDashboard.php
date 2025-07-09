<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\SessionManager;

class RoleDashboard
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
        $userType = SessionManager::getUserType();

        // Redirect to appropriate dashboard based on user type
        if ($request->is('student/*') && $userType !== 'student') {
            return redirect('/');
        }

        if ($request->is('admin/*') && $userType !== 'admin') {
            return redirect('/');
        }

        if ($request->is('professor/*') && $userType !== 'professor') {
            return redirect('/');
        }

        return $next($request);
    }
}
