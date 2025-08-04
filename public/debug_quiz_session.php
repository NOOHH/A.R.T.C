<?php
// Debug session and authentication for quiz taking
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

session_start();

echo "<h1>Quiz Debug - Session & Authentication Check</h1>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Laravel Session Data:</h2>";
echo "<pre>";
print_r(session()->all());
echo "</pre>";

echo "<h2>Authentication Check:</h2>";
$userRole = session('user_role');
$loggedIn = session('logged_in');
$userId = session('user_id');

echo "<p>User ID: " . ($userId ?? 'Not set') . "</p>";
echo "<p>User Role: " . ($userRole ?? 'Not set') . "</p>";
echo "<p>Logged In: " . ($loggedIn ? 'Yes' : 'No') . "</p>";

echo "<h2>Student Lookup:</h2>";
try {
    if ($userId) {
        $student = \App\Models\Student::where('user_id', $userId)->first();
        if ($student) {
            echo "<p>✅ Student found: ID {$student->student_id}</p>";
            echo "<p>Student data: " . json_encode($student->toArray()) . "</p>";
        } else {
            echo "<p>❌ No student found for user_id: {$userId}</p>";
            
            // Check all students
            $allStudents = \App\Models\Student::all();
            echo "<p>All students in database:</p>";
            foreach ($allStudents as $s) {
                echo "<p>- Student ID: {$s->student_id}, User ID: {$s->user_id}</p>";
            }
        }
    } else {
        echo "<p>❌ No user_id in session</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error checking student: " . $e->getMessage() . "</p>";
}

echo "<h2>Quiz Attempt Check:</h2>";
try {
    $attempt = \App\Models\QuizAttempt::find(3);
    if ($attempt) {
        echo "<p>✅ Quiz attempt found:</p>";
        echo "<pre>" . json_encode($attempt->toArray(), JSON_PRETTY_PRINT) . "</pre>";
        
        // Check if this student can access this attempt
        if ($userId) {
            $student = \App\Models\Student::where('user_id', $userId)->first();
            if ($student && $attempt->student_id === $student->student_id) {
                echo "<p>✅ Student can access this quiz attempt</p>";
            } else {
                echo "<p>❌ Student cannot access this quiz attempt</p>";
                echo "<p>Attempt student_id: {$attempt->student_id}, Current student_id: " . ($student ? $student->student_id : 'null') . "</p>";
            }
        }
    } else {
        echo "<p>❌ Quiz attempt 3 not found</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error checking quiz attempt: " . $e->getMessage() . "</p>";
}

echo "<h2>Session Fix Actions:</h2>";
echo "<a href='set_test_session.php' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Setup Student Session</a>";
echo "<a href='/student/quiz/take/3' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Try Quiz Again</a>";
echo "<a href='/debug-quiz-system' style='background: #6f42c1; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Debug System</a>";
?>
