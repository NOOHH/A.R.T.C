<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        // Set user as online after successful login
        if (session('logged_in') && session('user_id')) {
            User::where('user_id', session('user_id'))->update(['is_online' => true]);
        }
    }
    
    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        // Set user as offline before logout
        if (session('user_id')) {
            User::where('user_id', session('user_id'))->update(['is_online' => false]);
        }
        
        // Clear session data
        $request->session()->flush();
        
        // For ARTC, return to the ARTC homepage to keep the preview on ARTC
        return redirect()->route('artc.preview')->with('success', 'Logged out successfully');
    }
    
    /**
     * Update user online status via AJAX
     */
    public function updateOnlineStatus(Request $request)
    {
        $isOnline = $request->input('is_online', false);
        
        if (session('user_id')) {
            User::where('user_id', session('user_id'))
                ->update(['is_online' => $isOnline]);
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'error' => 'Not authenticated']);
    }
}
