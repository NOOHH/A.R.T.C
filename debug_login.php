<?php
/**
 * Debug Login Script
 * 
 * This script helps debug login issues by testing various aspects of the authentication system.
 * Run this from your browser or command line to get detailed information.
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Director;
use App\Models\Professor;
use App\Models\User;

echo "<h1>Login Debug Information</h1>\n";

// Test database connection
try {
    DB::connection()->getPdo();
    echo "<p style='color: green;'>✓ Database connection successful</p>\n";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>\n";
    exit;
}

// Test session configuration
echo "<h2>Session Configuration</h2>\n";
echo "<p>Session Driver: " . config('session.driver') . "</p>\n";
echo "<p>Session Domain: " . (config('session.domain') ?: 'null') . "</p>\n";
echo "<p>Session Secure: " . (config('session.secure') ? 'true' : 'false') . "</p>\n";
echo "<p>Session Same Site: " . config('session.same_site') . "</p>\n";

// Test user tables
echo "<h2>User Tables Check</h2>\n";

// Check admins table
$adminCount = Admin::count();
echo "<p>Admins table: {$adminCount} records</p>\n";

// Check directors table
$directorCount = Director::count();
echo "<p>Directors table: {$directorCount} records</p>\n";

// Check professors table
$professorCount = Professor::count();
echo "<p>Professors table: {$professorCount} records</p>\n";

// Check users table
$userCount = User::count();
echo "<p>Users table: {$userCount} records</p>\n";

// Show sample users (without passwords)
echo "<h2>Sample Users (First 3 from each table)</h2>\n";

echo "<h3>Admins:</h3>\n";
$admins = Admin::select('admin_id', 'admin_name', 'email')->limit(3)->get();
foreach ($admins as $admin) {
    echo "<p>ID: {$admin->admin_id}, Name: {$admin->admin_name}, Email: {$admin->email}</p>\n";
}

echo "<h3>Directors:</h3>\n";
$directors = Director::select('directors_id', 'directors_name', 'directors_email')->limit(3)->get();
foreach ($directors as $director) {
    echo "<p>ID: {$director->directors_id}, Name: {$director->directors_name}, Email: {$director->directors_email}</p>\n";
}

echo "<h3>Professors:</h3>\n";
$professors = Professor::select('professor_id', 'professor_name', 'professor_email')->limit(3)->get();
foreach ($professors as $professor) {
    echo "<p>ID: {$professor->professor_id}, Name: {$professor->professor_name}, Email: {$professor->professor_email}</p>\n";
}

echo "<h3>Users:</h3>\n";
$users = User::select('user_id', 'user_firstname', 'user_lastname', 'email')->limit(3)->get();
foreach ($users as $user) {
    echo "<p>ID: {$user->user_id}, Name: {$user->user_firstname} {$user->user_lastname}, Email: {$user->email}</p>\n";
}

// Test password hashing
echo "<h2>Password Hashing Test</h2>\n";
$testPassword = 'test123';
$hashedPassword = Hash::make($testPassword);
echo "<p>Test password: {$testPassword}</p>\n";
echo "<p>Hashed password: {$hashedPassword}</p>\n";
echo "<p>Hash check result: " . (Hash::check($testPassword, $hashedPassword) ? 'true' : 'false') . "</p>\n";

// Test CSRF token generation
echo "<h2>CSRF Token Test</h2>\n";
$csrfToken = csrf_token();
echo "<p>CSRF Token: {$csrfToken}</p>\n";

echo "<h2>Environment Information</h2>\n";
echo "<p>APP_ENV: " . env('APP_ENV') . "</p>\n";
echo "<p>APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "</p>\n";
echo "<p>APP_URL: " . env('APP_URL') . "</p>\n";

echo "<h2>Next Steps</h2>\n";
echo "<p>1. Check the Laravel logs at <code>storage/logs/laravel.log</code> for login attempts</p>\n";
echo "<p>2. Try logging in with one of the sample users above</p>\n";
echo "<p>3. Check browser developer tools for any JavaScript errors</p>\n";
echo "<p>4. Verify that cookies are being set properly</p>\n";
?>
