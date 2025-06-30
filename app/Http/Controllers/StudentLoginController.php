<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class StudentLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('Login.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        // First, try to find in admins table
        $admin = Admin::where('email', $credentials['email'])->first();
        
        if ($admin && Hash::check($credentials['password'], $admin->password)) {
            // Admin login successful
            session([
                'user_id' => $admin->admin_id,
                'user_name' => $admin->admin_name,
                'user_role' => 'admin',
                'logged_in' => true
            ]);
            return redirect()->route('admin.dashboard')->with('success', 'Admin logged in!');
        }

        // If not found in admins, try users table for students
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Check if user has student role
            if ($user->role === 'student') {
                // Store user info in session for authentication
                session([
                    'user_id' => $user->user_id,
                    'user_name' => $user->user_firstname . ' ' . $user->user_lastname,
                    'user_role' => $user->role,
                    'logged_in' => true
                ]);

                // Redirect to student dashboard
                return redirect()->route('student.dashboard')->with('success', 'Welcome back!');
            } else {
                // If user is unverified or has other role, deny access
                return back()->withErrors([
                    'email' => 'Your account is not verified or does not have proper access permissions.',
                ])->withInput($request->only('email'));
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        // Clear all session data
        $request->session()->flush();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}