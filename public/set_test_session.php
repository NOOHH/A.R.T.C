<?php
// Test login for quiz functionality
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

session_start();

// Get the student with user_id 15 (from your globals)
$student = \App\Models\Student::where('user_id', 15)->first();

if ($student) {
    // Set session for the actual student from your globals
    $_SESSION['user_id'] = 15;
    $_SESSION['logged_in'] = true;
    $_SESSION['user_type'] = 'student';
    $_SESSION['user_role'] = 'student';
    $_SESSION['username'] = 'Vince Michael Dela Vega';
    
    // Also set Laravel session
    session(['user_id' => 15]);
    session(['logged_in' => true]);
    session(['user_type' => 'student']);
    session(['user_role' => 'student']);
    session(['username' => 'Vince Michael Dela Vega']);
    
    echo "Session set for actual student:\n";
    echo "User ID: 15\n";
    echo "Student ID: " . $student->student_id . "\n";
    echo "Student Name: " . $student->student_fname . " " . $student->student_lname . "\n";
    echo "Logged in: Yes\n";
    echo "User type: student\n";
    echo "User role: student\n";
    
    echo "\nPHP Session:\n";
    print_r($_SESSION);
    
    echo "\nLaravel Session:\n";
    echo "user_id: " . session('user_id') . "\n";
    echo "user_role: " . session('user_role') . "\n";
    echo "logged_in: " . (session('logged_in') ? 'true' : 'false') . "\n";
    
} else {
    echo "Error: No student found with user_id 15\n";
    echo "Available students:\n";
    $students = \App\Models\Student::all();
    foreach ($students as $s) {
        echo "- Student ID: {$s->student_id}, User ID: {$s->user_id}, Name: {$s->student_fname} {$s->student_lname}\n";
    }
}

echo "\n<a href='/student/quiz/take/3' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Test Quiz Now</a>\n";
echo "<a href='/debug_quiz_session.php' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Debug Session</a>\n";
?>
