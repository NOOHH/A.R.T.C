<?php

namespace App\Http\Controllers\Smartprep\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Smartprep\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('smartprep.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $login = $request->input('email');
        $password = $request->input('password');

        // Step 1: Check if it's an admin user (admins table)
        $admin = Admin::where('email', $login)->first();
        
        if ($admin) {
            Log::info('SmartPrep Login: Admin found', ['email' => $login, 'admin_id' => $admin->id]);
            
            if (Hash::check($password, $admin->password)) {
                Log::info('SmartPrep Login: Admin password valid, logging in', ['admin_id' => $admin->id]);
                
                Auth::guard('admin')->login($admin, $request->boolean('remember'));
                $request->session()->regenerate();
                
                Log::info('SmartPrep Login: Admin login successful, redirecting to dashboard', ['admin_id' => $admin->id]);
                return redirect()->route('smartprep.admin.dashboard');
            } else {
                Log::warning('SmartPrep Login: Admin password invalid', ['email' => $login]);
                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])->onlyInput('email');
            }
        }

        // Step 2: Check if it's a regular user (users table)
        $user = User::where('email', $login)
            ->orWhere(function($q) use ($login) {
                $q->where('username', $login)->whereNotNull('username');
            })->first();

        if ($user) {
            Log::info('SmartPrep Login: User found', ['email' => $login, 'user_id' => $user->id, 'role' => $user->role]);
            
            if (Hash::check($password, $user->password)) {
                Log::info('SmartPrep Login: User password valid, logging in', ['user_id' => $user->id]);
                
                Auth::guard('smartprep')->login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                
                Log::info('SmartPrep Login: User login successful, redirecting to dashboard', ['user_id' => $user->id]);
                return redirect()->route('smartprep.dashboard');
            } else {
                Log::warning('SmartPrep Login: User password invalid', ['email' => $login]);
                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])->onlyInput('email');
            }
        }

        // Step 3: No user found in either table
        Log::warning('SmartPrep Login: No user found in either admins or users table', ['email' => $login]);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Check which guard the user is logged in with and logout accordingly
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } elseif (Auth::guard('smartprep')->check()) {
            Auth::guard('smartprep')->logout();
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('smartprep.login');
    }
}
