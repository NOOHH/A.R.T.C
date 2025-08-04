<?php
// Test file to simulate logged-in user and test routes
require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate a logged-in student session
session([
    'logged_in' => true,
    'user_id' => 16, // Based on the error logs showing user_id = 16
    'user_role' => 'student',
    'user_email' => 'test@example.com',
    'user_name' => 'Test User'
]);

// Also set the session using SessionManager to ensure consistency
\App\Helpers\SessionManager::set('user_id', 16);
\App\Helpers\SessionManager::set('user_role', 'student');
\App\Helpers\SessionManager::set('logged_in', true);

echo "Session data set:\n";
echo "user_id: " . session('user_id') . "\n";
echo "user_role: " . session('user_role') . "\n";
echo "logged_in: " . session('logged_in') . "\n";

// Test SessionManager
$sessionManager = new \App\Helpers\SessionManager();
echo "SessionManager isLoggedIn: " . ($sessionManager::isLoggedIn() ? 'true' : 'false') . "\n";
echo "SessionManager getUserType: " . $sessionManager::getUserType() . "\n";

// Test SessionManager context detection
echo "SessionManager isLaravelContext: " . (function_exists('app') && app()->bound('session') ? 'true' : 'false') . "\n";
echo "SessionManager get('user_id'): " . $sessionManager::get('user_id') . "\n";
echo "Laravel session('user_id'): " . session('user_id') . "\n";

// Check if student exists for user_id = 16
$student = \App\Models\Student::where('user_id', 16)->first();
echo "Student exists for user_id 16: " . ($student ? 'true' : 'false') . "\n";
if ($student) {
    echo "Student ID: " . $student->student_id . "\n";
} else {
    echo "No student found for user_id 16\n";
}

// Check what students exist in the database
$students = \App\Models\Student::take(5)->get();
echo "First 5 students in database:\n";
foreach ($students as $s) {
    echo "Student ID: " . $s->student_id . ", User ID: " . $s->user_id . "\n";
}

echo "\nTest completed.\n";
?> 