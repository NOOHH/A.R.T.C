<?php
/**
 * Debug Session and Authentication Issue
 * Identifies why quiz access is being denied
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;
use App\Models\User;
use App\Helpers\SessionManager;

echo "=== SESSION AND AUTHENTICATION DEBUG ===\n\n";

try {
    // 1. Check the specific quiz attempt from the database
    echo "1. Checking quiz attempt data...\n";
    $attempt = QuizAttempt::with(['quiz.questions', 'student'])->find(13); // Latest attempt from your data
    if ($attempt) {
        echo "✓ Found attempt ID: {$attempt->attempt_id}\n";
        echo "  - Quiz ID: {$attempt->quiz_id}\n";
        echo "  - Student ID: {$attempt->student_id}\n";
        echo "  - Status: {$attempt->status}\n";
        echo "  - Started at: {$attempt->started_at}\n";
        
        if ($attempt->quiz) {
            echo "  - Quiz title: {$attempt->quiz->quiz_title}\n";
            echo "  - Quiz status: {$attempt->quiz->status}\n";
        }
        
        if ($attempt->student) {
            echo "  - Student name: {$attempt->student->first_name} {$attempt->student->last_name}\n";
            echo "  - Student user_id: {$attempt->student->user_id}\n";
        }
    } else {
        echo "✗ Attempt not found\n";
    }
    echo "\n";
    
    // 2. Check the student with user_id 15 (from your globals)
    echo "2. Checking student with user_id 15...\n";
    $student = Student::where('user_id', 15)->first();
    if ($student) {
        echo "✓ Found student: {$student->student_id}\n";
        echo "  - Name: {$student->first_name} {$student->last_name}\n";
        echo "  - User ID: {$student->user_id}\n";
    } else {
        echo "✗ Student with user_id 15 not found\n";
    }
    echo "\n";
    
    // 3. Check the user with ID 15
    echo "3. Checking user with ID 15...\n";
    $user = User::find(15);
    if ($user) {
        echo "✓ Found user: {$user->email}\n";
        echo "  - Role: {$user->role}\n";
        echo "  - Name: {$user->name}\n";
    } else {
        echo "✗ User with ID 15 not found\n";
    }
    echo "\n";
    
    // 4. Simulate the session state
    echo "4. Simulating session state...\n";
    SessionManager::init();
    
    // Set session to match your globals
    SessionManager::set('user_id', 15);
    SessionManager::set('user_type', 'student');
    SessionManager::set('user_role', 'student');
    
    $sessionUserId = SessionManager::get('user_id');
    $sessionUserType = SessionManager::get('user_type');
    $sessionUserRole = SessionManager::get('user_role');
    
    echo "  - Session user_id: {$sessionUserId}\n";
    echo "  - Session user_type: {$sessionUserType}\n";
    echo "  - Session user_role: {$sessionUserRole}\n";
    echo "  - Session is logged in: " . (SessionManager::isLoggedIn() ? 'Yes' : 'No') . "\n";
    echo "\n";
    
    // 5. Test the takeQuiz method logic with the actual attempt
    echo "5. Testing takeQuiz method logic...\n";
    if ($attempt && $student) {
        // Simulate the takeQuiz method
        $userId = SessionManager::get('user_id');
        $currentStudent = Student::where('user_id', $userId)->first();
        $attemptData = QuizAttempt::with(['quiz.questions', 'student'])
            ->find($attempt->attempt_id);
        
        echo "  - Session user_id: {$userId}\n";
        echo "  - Current student found: " . ($currentStudent ? $currentStudent->student_id : 'NULL') . "\n";
        echo "  - Attempt student_id: {$attemptData->student_id}\n";
        
        // Check ownership
        if ($currentStudent && $attemptData->student_id === $currentStudent->student_id) {
            echo "  - ✓ Ownership verified\n";
        } else {
            echo "  - ✗ Ownership mismatch\n";
            echo "    Current student ID: " . ($currentStudent ? $currentStudent->student_id : 'NULL') . "\n";
            echo "    Attempt student ID: {$attemptData->student_id}\n";
        }
        
        // Check status
        if ($attemptData->status === 'in_progress') {
            echo "  - ✓ Status is in_progress\n";
        } else {
            echo "  - ✗ Status is not in_progress: {$attemptData->status}\n";
        }
        
        // Check if quiz is published
        if ($attemptData->quiz && $attemptData->quiz->status === 'published') {
            echo "  - ✓ Quiz is published\n";
        } else {
            echo "  - ✗ Quiz is not published: " . ($attemptData->quiz ? $attemptData->quiz->status : 'NULL') . "\n";
        }
    }
    echo "\n";
    
    // 6. Check if there's a mismatch between student IDs
    echo "6. Checking student ID consistency...\n";
    $allStudents = Student::where('user_id', 15)->get();
    echo "  Students with user_id 15:\n";
    foreach ($allStudents as $s) {
        echo "    - {$s->student_id} ({$s->first_name} {$s->last_name})\n";
    }
    
    if ($attempt) {
        $attemptStudent = Student::where('student_id', $attempt->student_id)->first();
        if ($attemptStudent) {
            echo "  Attempt student: {$attemptStudent->student_id} (user_id: {$attemptStudent->user_id})\n";
        } else {
            echo "  ✗ Attempt student not found\n";
        }
    }
    echo "\n";
    
    // 7. Check middleware and route access
    echo "7. Checking route and middleware...\n";
    $route = route('student.quiz.take', $attempt->attempt_id);
    echo "  - Generated route: {$route}\n";
    
    // Check if the route is accessible
    $routes = app('router')->getRoutes();
    $routeExists = false;
    foreach ($routes as $routeObj) {
        if ($routeObj->getName() === 'student.quiz.take') {
            $routeExists = true;
            $middleware = $routeObj->middleware();
            echo "  - Route middleware: " . implode(', ', $middleware) . "\n";
            break;
        }
    }
    echo "  - Route exists: " . ($routeExists ? 'Yes' : 'No') . "\n";
    echo "\n";
    
    // 8. Check for any recent errors in logs
    echo "8. Checking recent logs...\n";
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        $recentLines = array_slice($lines, -20); // Last 20 lines
        
        $errorLines = [];
        foreach ($recentLines as $line) {
            if (strpos($line, 'ERROR') !== false || strpos($line, 'error') !== false) {
                $errorLines[] = $line;
            }
        }
        
        if (!empty($errorLines)) {
            echo "  Recent errors found:\n";
            foreach (array_slice($errorLines, -5) as $error) {
                echo "    - " . trim($error) . "\n";
            }
        } else {
            echo "  No recent errors found\n";
        }
    } else {
        echo "  Log file not found\n";
    }
    echo "\n";
    
    echo "=== DEBUG COMPLETE ===\n";
    echo "Based on the analysis, the issue is likely:\n";
    echo "1. Student ID mismatch between session and attempt\n";
    echo "2. Quiz not published\n";
    echo "3. Middleware blocking access\n";
    echo "4. Session authentication issue\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 