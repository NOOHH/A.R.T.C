<?php

namespace App\Http\Controllers\Smartprep\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Smartprep\User;
use App\Models\Smartprep\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('smartprep.auth.login');
    }

    public function login(Request $request)
    {
        // Accept either email OR username (dynamic form configuration)
        $request->validate([
            'password' => ['required'],
        ]);
        if(!$request->filled('email') && !$request->filled('username')) {
            return back()->withErrors(['email' => 'Email or Username is required.']);
        }

        $login = $request->input('email') ?: $request->input('username');
        $password = $request->input('password');

        // Step 1: Check SmartPrep-admins table (main DB)
        $admin = Admin::where('email', $login)->first();
        if (
            $admin &&
            Str::endsWith(strtolower($admin->email), '@smartprep.com') &&
            Hash::check($password, $admin->password)
        ) {
            Log::info('SmartPrep Login: Admin (main DB) authenticated', ['admin_pk' => $admin->getKey()]);
            Auth::guard('smartprep_admin')->login($admin, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->route('smartprep.admin.dashboard');
        }

        // Step 1b: Try tenant admins table and sync to SmartPrep if valid
        try {
            $tenantAdmin = \App\Models\Admin::where('email', $login)->first();
            if (
                $tenantAdmin &&
                Str::endsWith(strtolower($tenantAdmin->email), '@smartprep.com') &&
                Hash::check($password, $tenantAdmin->password)
            ) {
                // Sync or create SmartPrep admin with same credentials
                $synced = Admin::firstOrNew(['email' => $tenantAdmin->email]);
                $synced->name = $tenantAdmin->admin_name ?? ($tenantAdmin->name ?? 'Admin');
                $synced->password = $tenantAdmin->password; // already hashed
                $synced->save();

                Auth::guard('smartprep_admin')->login($synced, $request->boolean('remember'));
                $request->session()->regenerate();
                return redirect()->route('smartprep.admin.dashboard');
            }
        } catch (\Throwable $e) {
            Log::error('SmartPrep Login: tenant admin sync check failed', ['error' => $e->getMessage()]);
        }

        // Step 2: Check if it's a regular user (users table)
        $user = User::where('email', $login)
            ->orWhere(function($q) use ($login) {
                $q->where('username', $login)->whereNotNull('username');
            })
            ->first();

        if ($user) {
            Log::info('SmartPrep Login: User found', ['email' => $login, 'user_id' => $user->getKey(), 'role' => $user->role]);
            
            if (Hash::check($password, $user->password)) {
                Log::info('SmartPrep Login: User password valid, logging in', ['user_id' => $user->getKey()]);
                
                Auth::guard('smartprep')->login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                
                // Redirect based on user role
                if ($user->role === 'admin') {
                    Log::info('SmartPrep Login: Admin user redirecting to admin dashboard', ['user_id' => $user->getKey()]);
                    return redirect()->route('smartprep.admin.dashboard');
                } else {
                    Log::info('SmartPrep Login: Client user redirecting to client dashboard', ['user_id' => $user->getKey(), 'role' => $user->role]);
                    return redirect()->route('smartprep.dashboard');
                }
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
        if (Auth::guard('smartprep_admin')->check()) {
            Auth::guard('smartprep_admin')->logout();
        } elseif (Auth::guard('smartprep')->check()) {
            Auth::guard('smartprep')->logout();
        }
        
        // Invalidate SmartPrep-scoped session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Proactively clear ARTC/root cookies so the preview logs out too
        try {
            // Root Laravel session (ARTC)
            Cookie::queue(cookie()->forget('laravel_session', '/'));
            // Root XSRF token used by ARTC
            Cookie::queue(cookie()->forget('XSRF-TOKEN', '/'));
            // SmartPrep-scoped session cookie
            Cookie::queue(cookie()->forget('smartprep_session', '/smartprep'));
            // Any remember cookies
            foreach ($request->cookies->keys() as $cookieName) {
                if (Str::startsWith($cookieName, 'remember_')) {
                    Cookie::queue(cookie()->forget($cookieName, '/'));
                }
            }
        } catch (\Throwable $e) {
            // Best effort only; ignore
        }

        return redirect()->route('smartprep.login');
    }
}
