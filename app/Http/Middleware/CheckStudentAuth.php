<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckStudentAuth
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
        // Allow preview mode to bypass authentication completely
        if ($request->boolean('preview', false)) {
            return $next($request);
        }

        // Check if user is logged in via session
        if (!session('logged_in') || !session('user_id')) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Get session data for debugging
        $userId = session('user_id');
        $userRole = session('user_role');
        $userType = session('user_type');
        
        // Check if user is actually an admin, director, or professor trying to access student dashboard
        if ($userType && $userType !== 'student') {
            Log::warning('Non-student user trying to access student dashboard', [
                'user_id' => $userId,
                'user_type' => $userType,
                'user_role' => $userRole,
                'user_name' => session('user_name'),
                'request_url' => $request->url()
            ]);
            
            // Redirect based on actual type
            if ($userType === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Please use the admin dashboard.');
            } elseif ($userType === 'professor') {
                return redirect()->route('professor.dashboard')->with('error', 'Please use the professor dashboard.');
            } elseif ($userType === 'director') {
                return redirect()->route('director.dashboard')->with('error', 'Please use the director dashboard.');
            }
        }

        // Check if user is a student - be more specific about student role
        if ($userRole !== 'student') {
            // Double-check by looking up in database if needed
            $isStudentInDB = DB::table('users')->where('user_id', $userId)->where('role', 'student')->exists();
            
            if (!$isStudentInDB) {
                Log::warning('Student auth failed - not a student in database', [
                    'user_id' => $userId,
                    'user_role' => $userRole,
                    'user_type' => $userType,
                    'user_name' => session('user_name'),
                    'db_check' => 'failed'
                ]);
                
                // Clear potentially corrupted session
                session()->flush();
                return redirect()->route('login')->with('error', 'Session invalid. Please log in again.');
            }
        }

        return $next($request);
    }
}
