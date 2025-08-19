<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckProfessorAuth
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

        // Debug logging
        Log::info('Professor Auth Check', [
            'session_logged_in' => session('logged_in'),
            'session_professor_id' => session('professor_id'),
            'session_user_role' => session('user_role'),
            'session_user_type' => session('user_type'),
            'session_user_id' => session('user_id'),
            'auth_check' => auth()->check(),
            'auth_user' => auth()->user()
        ]);

        // Check if professor is logged in via session (using UnifiedLoginController variables)
        if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
            Log::warning('Professor Auth Failed', [
                'logged_in' => session('logged_in'),
                'professor_id' => session('professor_id'),
                'user_role' => session('user_role'),
                'user_type' => session('user_type')
            ]);
            
            // Clear any conflicting session data
            session()->forget(['directors_id', 'admin_id']);
            session(['user_role' => 'professor', 'user_type' => 'professor']);
            
            return redirect()->route('login')->with('error', 'Please log in as a professor to access this page.');
        }

        // Ensure session variables are properly set and consistent
        if (!session('user_role') || session('user_role') !== 'professor') {
            session(['user_role' => 'professor', 'user_type' => 'professor']);
        }
        
        // Ensure user_id matches professor_id for professors
        if (session('professor_id') && session('user_id') !== session('professor_id')) {
            session(['user_id' => session('professor_id')]);
        }

        Log::info('Professor Auth Success', [
            'professor_id' => session('professor_id'),
            'user_role' => session('user_role'),
            'user_id' => session('user_id')
        ]);

        return $next($request);
    }
}
