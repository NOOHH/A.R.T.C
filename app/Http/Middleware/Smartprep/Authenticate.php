<?php

namespace App\Http\Middleware\Smartprep;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        // Check both smartprep guard (for regular users) and admin guard (for admin users)
        $isAuthenticated = Auth::guard('smartprep')->check() || Auth::guard('admin')->check();
        
        if (!$isAuthenticated) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            return redirect()->route('smartprep.login');
        }

        return $next($request);
    }
}
