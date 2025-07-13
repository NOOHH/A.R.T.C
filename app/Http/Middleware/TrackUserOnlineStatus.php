<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class TrackUserOnlineStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in via session
        $userId = null;
        
        if (session('logged_in') && session('user_id')) {
            $userId = session('user_id');
        }
        
        if ($userId) {
            // Update user's online status
            User::where('user_id', $userId)->update(['is_online' => true]);
        }

        return $next($request);
    }
}
