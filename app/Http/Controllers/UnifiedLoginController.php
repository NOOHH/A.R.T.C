<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Professor;
use App\Models\Admin;
use App\Models\Director;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * UnifiedLoginController
 * 
 * This controller handles login for all user types in the system:
 * - Admin (from admins table)
 * - Director (from directors table)  
 * - Professor (from professors table)
 * - Student/User (from users table)
 * 
 * MERGED FROM: StudentLoginController.php
 * All functionality from the original StudentLoginController has been
 * preserved and integrated into this unified system.
 * 
 * Priority Order: Admin -> Director -> Professor -> Student
 * This ensures higher privilege users get priority access.
 */
class UnifiedLoginController extends Controller
{
    /**
     * Show the unified login form
     */
    public function showLoginForm()
    {
        return view('Login.login');
    }

    /**
     * Handle login for all user types: Admin, Student, Professor, Director
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $email = $request->email;
        $password = $request->password;

        // Priority order: Admin -> Director -> Professor -> Student (Users table)
        // This ensures admins and directors have priority access

        // 1. Check if user is an admin
        $admin = Admin::where('email', $email)->first();
        if ($admin) {
            return $this->loginAdmin($admin, $password, $request);
        }

        // 2. Check if user is a director
        $director = Director::where('directors_email', $email)->first();
        if ($director) {
            return $this->loginDirector($director, $password, $request);
        }

        // 3. Check if user is a professor
        $professor = Professor::where('professor_email', $email)->first();
        if ($professor) {
            return $this->loginProfessor($professor, $password, $request);
        }

        // 4. Check users table for any user (students, unverified, etc.) - preserving original behavior
        $user = User::where('email', $email)->first();
        if ($user) {
            return $this->loginStudent($user, $password, $request);
        }

        // If no account found in any table
        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->withInput($request->only('email'));
    }

    /**
     * Handle student login
     */
    private function loginStudent($user, $password, $request)
    {
        // Verify password
        if (!Hash::check($password, $user->password)) {
            return back()->withErrors(['password' => 'The password is incorrect.'])->withInput();
        }

        // Allow all users to access dashboard (students, unverified, etc.) - preserving original behavior
        // Store user info in session for authentication (using exact same format as original StudentLoginController)
        session([
            'user_id' => $user->user_id,
            'user_name' => $user->user_firstname . ' ' . $user->user_lastname,
            'user_firstname' => $user->user_firstname,
            'user_lastname' => $user->user_lastname,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'role'      => $user->role,
            'logged_in' => true
        ]);

        Log::info('Student logged in successfully', ['user_id' => $user->user_id]);

        // Check if user is coming from enrollment process (preserve exact original functionality)
        if ($request->has('from_enrollment') && $request->from_enrollment === 'true') {
            // Store email in session to help with auto-fill if needed
            session(['user_email' => $user->email]);
            
            // Check if it's from modular enrollment
            $referer = $request->headers->get('referer');
            if (strpos($referer, 'modular') !== false) {
                return redirect()->route('enrollment.modular')
                    ->with('success', 'Welcome back! Continue your enrollment process.');
            }
            
            // Default to full enrollment (the most common case)
            return redirect()->route('enrollment.full')
                ->with('success', 'Welcome back! Continue your enrollment process.');
        }

        // Default redirect to student dashboard
        return redirect()->route('student.dashboard')->with('success', 'Welcome back!');
    }

    /**
     * Handle professor login
     */
    private function loginProfessor($professor, $password, $request)
    {
        // Check if professor is archived
        if ($professor->professor_archived) {
            return back()->withErrors(['email' => 'This professor account has been archived.'])->withInput();
        }

        // Verify password - check if it's hashed or plain text
        $passwordMatches = false;
        
        // First try with hashed password
        if (Hash::check($password, $professor->professor_password)) {
            $passwordMatches = true;
        } 
        // If that fails, try plain text (for accounts created by admin)
        else if ($password === $professor->professor_password) {
            $passwordMatches = true;
            
            // Hash the password for future use
            $professor->professor_password = Hash::make($password);
            $professor->save();
        }

        if (!$passwordMatches) {
            return back()->withErrors(['password' => 'The password is incorrect.'])->withInput();
        }

        // Create session using PHP sessions (not Laravel sessions)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $professor->professor_id;
        $_SESSION['user_type'] = 'professor';
        $_SESSION['user_name'] = $professor->full_name;
        $_SESSION['user_email'] = $professor->professor_email;
        $_SESSION['logged_in'] = true;

        // Also set Laravel session for middleware compatibility
        session([
            'professor_id' => $professor->professor_id,
            'user_id' => $professor->professor_id,
            'user_name' => $professor->full_name,
            'user_email' => $professor->professor_email,
            'user_type' => 'professor',
            'user_role' => 'professor',
            'role'      => 'professor',
            'logged_in' => true
        ]);

        Log::info('Professor logged in successfully', ['professor_id' => $professor->professor_id]);

        // Redirect to professor dashboard
        return redirect()->route('professor.dashboard')->with('success', 'Welcome back, ' . $professor->full_name . '!');
    }

    /**
     * Handle admin login
     */
    private function loginAdmin($admin, $password, $request)
    {
        // Verify password
        if (!Hash::check($password, $admin->password)) {
            return back()->withErrors(['password' => 'The password is incorrect.'])->withInput();
        }

        // Create session using PHP sessions (not Laravel sessions)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $admin->admin_id;
        $_SESSION['user_type'] = 'admin';
        $_SESSION['user_name'] = $admin->admin_name;
        $_SESSION['user_email'] = $admin->email;
        $_SESSION['logged_in'] = true;

        // Also set Laravel session for middleware compatibility
        session([
            'user_id' => $admin->admin_id,
            'user_name' => $admin->admin_name,
            'user_email' => $admin->email,
            'user_type' => 'admin',  // Added this for consistency
            'user_role' => 'admin',
            'role'       => 'admin',
            'logged_in' => true
        ]);

        Log::info('Admin logged in successfully', ['admin_id' => $admin->admin_id]);

        // Redirect to admin dashboard (preserving original success message format)
        return redirect()->route('admin.dashboard')->with('success', 'Admin logged in!');
    }

    /**
     * Handle director login
     */
    private function loginDirector($director, $password, $request)
    {
        // Check if director is archived
        if ($director->directors_archived) {
            return back()->withErrors(['email' => 'This director account has been archived.'])->withInput();
        }

        // Verify password - check if it's hashed or plain text (similar to professor logic)
        $passwordMatches = false;
        
        // First try with hashed password
        if (Hash::check($password, $director->directors_password)) {
            $passwordMatches = true;
        } 
        // If that fails, try plain text (for accounts created by admin)
        else if ($password === $director->directors_password) {
            $passwordMatches = true;
            
            // Hash the password for future use
            $director->directors_password = Hash::make($password);
            $director->save();
        }

        if (!$passwordMatches) {
            return back()->withErrors(['password' => 'The password is incorrect.'])->withInput();
        }

        // Create session using PHP sessions (not Laravel sessions)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $director->directors_id;
        $_SESSION['user_type'] = 'director';
        $_SESSION['user_name'] = $director->directors_name;
        $_SESSION['user_email'] = $director->directors_email;
        $_SESSION['logged_in'] = true;

        // Also set Laravel session for middleware compatibility
        session([
            'directors_id' => $director->directors_id,
            'user_id' => $director->directors_id,
            'user_name' => $director->directors_name,
            'user_email' => $director->directors_email,
            'user_type' => 'director',
            'user_role' => 'director',
            'role'      => 'director',
            'logged_in' => true
        ]);

        Log::info('Director logged in successfully', ['directors_id' => $director->directors_id]);

        // Redirect to director dashboard
        return redirect()->route('director.dashboard')->with('success', 'Welcome back, ' . $director->directors_name . '!');
    }

    /**
     * Handle logout for all user types
     */
    public function logout(Request $request)
    {
        // Clear all session data
        $request->session()->flush();
        $request->session()->regenerateToken();
        
        // Redirect to home page (preserving original behavior)
        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Check if email exists across all user tables
     */
    public static function isEmailUnique($email, $excludeId = null, $excludeTable = null)
    {
        // Check admins table
        $adminExists = Admin::where('email', $email)->exists();
        if ($adminExists && !($excludeTable === 'admins' && $excludeId)) {
            return false;
        }

        // Check directors table
        $directorExists = Director::where('directors_email', $email)->exists();
        if ($directorExists && !($excludeTable === 'directors' && $excludeId)) {
            return false;
        }

        // Check professors table
        $professorExists = Professor::where('professor_email', $email)->exists();
        if ($professorExists && !($excludeTable === 'professors' && $excludeId)) {
            return false;
        }

        // Check users table (students)
        $userExists = User::where('email', $email)->exists();
        if ($userExists && !($excludeTable === 'users' && $excludeId)) {
            return false;
        }

        return true; // Email is unique
    }

    /**
     * Sync user to users table when creating professor/director accounts
     */
    public static function syncToUsersTable($email, $name, $role, $password = null, $recordId = null)
    {
        // Check if user already exists in users table
        $existingUser = User::where('email', $email)->first();
        
        if (!$existingUser) {
            $userData = [
                'email' => $email,
                'user_firstname' => $name,
                'user_lastname' => '',
                'password' => $password ? Hash::make($password) : Hash::make('default123'),
                'role' => $role,
                'admin_id' => session('admin_id') ?? 1,
                'directors_id' => null // Set default null value
            ];

            // Add the appropriate ID based on role
            if ($role === 'director' && $recordId) {
                $userData['directors_id'] = $recordId;
            }

            // Create user record
            $user = User::create($userData);
            
            Log::info("User synced to users table", [
                'email' => $email,
                'role' => $role,
                'user_id' => $user->user_id,
                'record_id' => $recordId
            ]);
            
            return $user;
        }
        
        return $existingUser;
    }
}
