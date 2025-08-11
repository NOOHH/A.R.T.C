<?php
/**
 * Temporary fix for 419 errors - adds debug routes and CSRF bypass for testing
 * REMOVE THIS AFTER FIXING THE MAIN ISSUE
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

// Debug route to check session status
Route::get('/debug-session', function (Request $request) {
    return response()->json([
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'session_data' => session()->all(),
        'cookie_params' => session()->getCookieParams(),
        'domain' => config('session.domain'),
        'secure' => config('session.secure'),
        'same_site' => config('session.same_site'),
        'request_host' => $request->getHost(),
        'request_scheme' => $request->getScheme(),
    ]);
})->name('debug.session');

// Test login route with CSRF protection disabled temporarily
Route::post('/test-login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
    
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return response()->json([
            'status' => 'success',
            'message' => 'Login successful!',
            'redirect' => '/dashboard'
        ]);
    }
    
    return response()->json([
        'status' => 'error', 
        'message' => 'Invalid credentials'
    ], 422);
})->middleware('web')->name('test.login');

?>
