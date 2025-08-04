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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\PasswordResetMail;

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
            'user_role' => 'student', // Force role to student for users table
            'role'      => 'student', // Force role to student for users table
            'logged_in' => true
        ]);

        // Also set SessionManager variables for compatibility with CheckSession middleware
        \App\Helpers\SessionManager::init();
        \App\Helpers\SessionManager::set('user_id', $user->user_id);
        \App\Helpers\SessionManager::set('user_name', $user->user_firstname . ' ' . $user->user_lastname);
        \App\Helpers\SessionManager::set('user_firstname', $user->user_firstname);
        \App\Helpers\SessionManager::set('user_lastname', $user->user_lastname);
        \App\Helpers\SessionManager::set('user_email', $user->email);
        \App\Helpers\SessionManager::set('user_role', 'student');
        \App\Helpers\SessionManager::set('user_type', 'student');

        Log::info('Student logged in successfully', ['user_id' => $user->user_id, 'role' => 'student']);

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

        // Authenticate using Laravel's professor guard
        Auth::guard('professor')->login($professor);

        // (Optional: keep legacy session variables for compatibility)
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

        // Also set SessionManager variables for compatibility with CheckSession middleware
        \App\Helpers\SessionManager::init();
        \App\Helpers\SessionManager::set('user_id', $professor->professor_id);
        \App\Helpers\SessionManager::set('user_name', $professor->full_name);
        \App\Helpers\SessionManager::set('user_email', $professor->professor_email);
        \App\Helpers\SessionManager::set('user_role', 'professor');
        \App\Helpers\SessionManager::set('user_type', 'professor');

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

        // Authenticate admin with Laravel Auth (multi-auth guard) - THIS WAS MISSING!
        Auth::guard('admin')->login($admin);

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

        // Also set SessionManager variables for compatibility with CheckSession middleware
        \App\Helpers\SessionManager::init();
        \App\Helpers\SessionManager::set('user_id', $admin->admin_id);
        \App\Helpers\SessionManager::set('user_name', $admin->admin_name);
        \App\Helpers\SessionManager::set('user_email', $admin->email);
        \App\Helpers\SessionManager::set('user_role', 'admin');
        \App\Helpers\SessionManager::set('user_type', 'admin');

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

        // Authenticate director with Laravel Auth (multi-auth guard)
        Auth::guard('director')->login($director);

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

        // Also set SessionManager variables for compatibility with CheckSession middleware
        \App\Helpers\SessionManager::init();
        \App\Helpers\SessionManager::set('user_id', $director->directors_id);
        \App\Helpers\SessionManager::set('user_name', $director->directors_name);
        \App\Helpers\SessionManager::set('user_email', $director->directors_email);
        \App\Helpers\SessionManager::set('user_role', 'director');
        \App\Helpers\SessionManager::set('user_type', 'director');

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
        
        // Also clear SessionManager variables
        \App\Helpers\SessionManager::init();
        \App\Helpers\SessionManager::destroy();
        
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

            // Create user record with error suppression for warnings
            try {
                // Temporarily disable strict mode to suppress warnings
                DB::statement("SET sql_mode = ''");
                
                $user = User::create($userData);
                
                // Restore strict mode
                DB::statement("SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
                
                Log::info("User synced to users table", [
                    'email' => $email,
                    'role' => $role,
                    'user_id' => $user->user_id,
                    'record_id' => $recordId
                ]);
                
                return $user;
            } catch (\Exception $e) {
                // Restore strict mode in case of error
                DB::statement("SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
                
                // If it's just a warning about data truncation, we can ignore it
                if (strpos($e->getMessage(), 'Data truncated') !== false) {
                    // Try to get the user that was created despite the warning
                    $user = User::where('email', $email)->first();
                    if ($user) {
                        Log::info("User synced to users table (with warning suppressed)", [
                            'email' => $email,
                            'role' => $role,
                            'user_id' => $user->user_id,
                            'record_id' => $recordId
                        ]);
                        return $user;
                    }
                }
                
                // Re-throw the exception if it's not a data truncation warning
                throw $e;
            }
        }
        
        return $existingUser;
    }

    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('Login.password-reset');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Log the request for debugging
        Log::info('Password reset request received', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Check if email exists in any of the user tables
        $userExists = false;
        $userType = null;
        $userName = null;
        
        try {
            // Check students table (uses 'email' column)
            if (Student::where('email', $request->email)->exists()) {
                $student = Student::where('email', $request->email)->first();
                $userExists = true;
                $userType = 'student';
                $userName = trim(($student->firstname ?? '') . ' ' . ($student->lastname ?? ''));
                Log::info('User found in students table', ['email' => $request->email, 'user_id' => $student->student_id]);
            }
            // Check admins table (uses 'email' column)
            elseif (Admin::where('email', $request->email)->exists()) {
                $admin = Admin::where('email', $request->email)->first();
                $userExists = true;
                $userType = 'admin';
                $userName = $admin->admin_name ?? 'Admin';
                Log::info('User found in admins table', ['email' => $request->email, 'admin_id' => $admin->admin_id]);
            }
            // Check professors table (uses 'professor_email' column)
            elseif (Professor::where('professor_email', $request->email)->exists()) {
                $professor = Professor::where('professor_email', $request->email)->first();
                $userExists = true;
                $userType = 'professor';
                $userName = trim(($professor->professor_first_name ?? '') . ' ' . ($professor->professor_last_name ?? '')) ?: $professor->professor_name ?? 'Professor';
                Log::info('User found in professors table', ['email' => $request->email, 'professor_id' => $professor->professor_id]);
            }
            // Check directors table (uses 'directors_email' column)
            elseif (Director::where('directors_email', $request->email)->exists()) {
                $director = Director::where('directors_email', $request->email)->first();
                $userExists = true;
                $userType = 'director';
                $userName = trim(($director->directors_first_name ?? '') . ' ' . ($director->directors_last_name ?? '')) ?: $director->directors_name ?? 'Director';
                Log::info('User found in directors table', ['email' => $request->email, 'director_id' => $director->director_id]);
            }

            if ($userExists) {
                // Generate password reset token
                $token = Str::random(64);
                
                // Clear any existing tokens for this email
                DB::table('password_resets')->where('email', $request->email)->delete();
                
                // Store the token in database with expiration (1 hour)
                DB::table('password_resets')->insert([
                    'email' => $request->email,
                    'token' => $token,
                    'user_type' => $userType,
                    'created_at' => now()
                ]);
                
                // Generate reset URL
                $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($request->email));
                
                Log::info('Attempting to send password reset email', [
                    'email' => $request->email,
                    'user_type' => $userType,
                    'reset_url' => $resetUrl
                ]);
                
                // Send password reset email using the same method as OTP
                Mail::raw("Hello {$userName},\n\nYou have requested to reset your password for your A.R.T.C account.\n\nClick the following link to reset your password:\n{$resetUrl}\n\nThis link will expire in 1 hour.\n\nIf you did not request this password reset, please ignore this email.\n\nBest regards,\nA.R.T.C Team", function ($message) use ($request) {
                    $message->to($request->email)
                            ->subject('A.R.T.C - Password Reset Request');
                });
                
                Log::info('Password reset email sent successfully', [
                    'email' => $request->email, 
                    'user_type' => $userType,
                    'user_name' => $userName
                ]);
            } else {
                Log::info('Password reset requested for non-existent email', [
                    'email' => $request->email
                ]);
            }
            
            // Always return the same message for security (don't reveal if email exists)
            return back()->with(['status' => 'If your email address is in our system, you will receive a password reset link shortly.']);
            
        } catch (\Exception $e) {
            Log::error('Password reset email failed: ' . $e->getMessage(), [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Still return success message for security, but log the error
            return back()->with(['status' => 'If your email address is in our system, you will receive a password reset link shortly.']);
        }
    }

    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request, $token = null)
    {
        if (!$token) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Invalid password reset token.']);
        }
        
        // Look up token in database
        $resetRecord = DB::table('password_resets')->where('token', $token)->first();
        
        if (!$resetRecord) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Invalid password reset token.']);
        }
        
        // Check if token is expired (1 hour)
        $tokenAge = now()->diffInMinutes($resetRecord->created_at);
        if ($tokenAge > 60) {
            // Delete expired token
            DB::table('password_resets')->where('token', $token)->delete();
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Password reset token has expired. Please request a new one.']);
        }
        
        // Use email from request or from database record
        $email = $request->email ?: $resetRecord->email;
        
        return view('Login.change-password')->with([
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, lowercase letter, number, and special character.'
        ]);

        // Validate reCAPTCHA if enabled
        if (env('RECAPTCHA_SECRET_KEY')) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if (!$recaptchaResponse) {
                return back()->withErrors(['captcha' => 'Please complete the reCAPTCHA verification.']);
            }

            try {
                $response = Http::post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->ip()
                ]);

                $responseData = $response->json();
                if (!$responseData['success']) {
                    return back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.']);
                }
            } catch (\Exception $e) {
                Log::error('reCAPTCHA verification failed: ' . $e->getMessage());
                return back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.']);
            }
        }

        // Validate token using database
        $resetRecord = DB::table('password_resets')->where('token', $request->token)->first();
        
        if (!$resetRecord) {
            return back()->withErrors(['token' => 'Invalid password reset token.']);
        }
        
        // Check if token is expired (1 hour)
        $tokenAge = now()->diffInMinutes($resetRecord->created_at);
        if ($tokenAge > 60) {
            // Delete expired token
            DB::table('password_resets')->where('token', $request->token)->delete();
            return back()->withErrors(['token' => 'Password reset token has expired. Please request a new one.']);
        }
        
        if ($request->email !== $resetRecord->email) {
            return back()->withErrors(['email' => 'Email address does not match the reset request.']);
        }

        // Validate reCAPTCHA if enabled
        if (env('RECAPTCHA_SECRET_KEY')) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if (!$recaptchaResponse) {
                return back()->withErrors(['captcha' => 'Please complete the reCAPTCHA verification.']);
            }

            try {
                $response = Http::post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->ip()
                ]);

                $responseData = $response->json();
                if (!$responseData['success']) {
                    return back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.']);
                }
            } catch (\Exception $e) {
                Log::error('reCAPTCHA verification failed: ' . $e->getMessage());
                return back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.']);
            }
        }

        // Find user in appropriate table and update password
        $email = $request->email;
        $newPassword = Hash::make($request->password);
        $updated = false;

        try {
            // Check each user type and update password using correct email column
            if (Student::where('email', $email)->exists()) {
                Student::where('email', $email)->update(['password' => $newPassword]);
                $updated = true;
                Log::info('Password reset successful for student: ' . $email);
            }
            elseif (Admin::where('email', $email)->exists()) {
                Admin::where('email', $email)->update(['password' => $newPassword]);
                $updated = true;
                Log::info('Password reset successful for admin: ' . $email);
            }
            elseif (Professor::where('professor_email', $email)->exists()) {
                Professor::where('professor_email', $email)->update(['professor_password' => $newPassword]);
                $updated = true;
                Log::info('Password reset successful for professor: ' . $email);
            }
            elseif (Director::where('directors_email', $email)->exists()) {
                Director::where('directors_email', $email)->update(['directors_password' => $newPassword]);
                $updated = true;
                Log::info('Password reset successful for director: ' . $email);
            }

            if ($updated) {
                // Clear the reset token after successful password update
                DB::table('password_resets')->where('token', $request->token)->delete();
                
                return redirect()->route('login')->with('status', 'Your password has been reset successfully! You can now log in with your new password.');
            }

            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
            
        } catch (\Exception $e) {
            Log::error('Password reset failed: ' . $e->getMessage(), [
                'email' => $email,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->withErrors(['email' => 'There was an error updating your password. Please try again.']);
        }
    }
}
