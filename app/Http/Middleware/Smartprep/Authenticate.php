<?php

namespace App\Http\Middleware\Smartprep;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        // Check both smartprep guard (for regular users) and smartprep_admin guard (for admin users)
        $isAuthenticated = Auth::guard('smartprep')->check() || Auth::guard('smartprep_admin')->check();
        
        if (!$isAuthenticated) {
            Log::warning('SmartPrep.Auth middleware: unauthenticated redirect', [
                'path' => $request->path(),
                'session_id' => session()->getId(),
                'smartprep_check' => Auth::guard('smartprep')->check(),
                'smartprep_admin_check' => Auth::guard('smartprep_admin')->check(),
                'cookies' => array_keys($request->cookies->all()),
            ]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            return redirect()->route('smartprep.login');
        }

        return $next($request);
    }
}
