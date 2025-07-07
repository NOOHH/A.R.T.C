<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Professor;

class ProfessorLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('professor.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        // Try to find professor by email
        $professor = Professor::where('professor_email', $credentials['email'])->first();
        
        if ($professor && Hash::check($credentials['password'], $professor->professor_password)) {
            // Check if professor is not archived
            if (!$professor->professor_archived) {
                // Store professor info in session for authentication
                session([
                    'professor_id' => $professor->professor_id,
                    'professor_name' => $professor->full_name,
                    'professor_email' => $professor->professor_email,
                    'professor_role' => 'professor',
                    'professor_logged_in' => true
                ]);

                return redirect()->route('professor.dashboard')->with('success', 'Welcome to Professor Dashboard!');
            } else {
                return back()->withErrors([
                    'email' => 'Your account has been archived. Please contact the administrator.',
                ])->withInput($request->only('email'));
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        // Clear professor session data
        $request->session()->forget([
            'professor_id',
            'professor_name', 
            'professor_email',
            'professor_role',
            'professor_logged_in'
        ]);
        
        $request->session()->regenerateToken();
        
        return redirect()->route('professor.login')->with('success', 'You have been logged out successfully.');
    }
}
