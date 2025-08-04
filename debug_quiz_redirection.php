<?php
/**
 * Comprehensive Quiz Redirection Debug Script
 * This script tests all components of the quiz system to identify why
 * quiz attempts redirect to dashboard instead of the take.blade.php view
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;
use App\Models\User;
use App\Helpers\SessionManager;

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== QUIZ REDIRECTION DEBUG SCRIPT ===\n\n";

try {
    // 1. Test Database Connection
    echo "1. Testing Database Connection...\n";
    $dbTest = DB::select('SELECT 1 as test');
    echo "✓ Database connection successful\n\n";

    // 2. Check Quiz Tables
    echo "2. Checking Quiz Tables...\n";
    $quizCount = Quiz::count();
    $attemptCount = QuizAttempt::count();
    echo "✓ Quizzes found: {$quizCount}\n";
    echo "✓ Quiz attempts found: {$attemptCount}\n\n";

    // 3. Check Sample Quiz Data
    echo "3. Checking Sample Quiz Data...\n";
    $sampleQuiz = Quiz::with('questions')->first();
    if ($sampleQuiz) {
        echo "✓ Sample quiz found: {$sampleQuiz->quiz_title}\n";
        echo "  - Status: {$sampleQuiz->status}\n";
        echo "  - Questions: " . $sampleQuiz->questions->count() . "\n";
        echo "  - Max attempts: {$sampleQuiz->max_attempts}\n";
    } else {
        echo "✗ No quizzes found in database\n";
    }
    echo "\n";

    // 4. Check Sample Student Data
    echo "4. Checking Sample Student Data...\n";
    $sampleStudent = Student::first();
    if ($sampleStudent) {
        echo "✓ Sample student found: {$sampleStudent->student_id}\n";
        echo "  - User ID: {$sampleStudent->user_id}\n";
        echo "  - Name: {$sampleStudent->first_name} {$sampleStudent->last_name}\n";
    } else {
        echo "✗ No students found in database\n";
    }
    echo "\n";

    // 5. Test Session Management
    echo "5. Testing Session Management...\n";
    SessionManager::init();
    
    // Simulate student login
    if ($sampleStudent) {
        SessionManager::set('user_id', $sampleStudent->user_id);
        SessionManager::set('user_type', 'student');
        SessionManager::set('user_role', 'student');
        
        $sessionUserId = SessionManager::get('user_id');
        $sessionUserType = SessionManager::get('user_type');
        
        echo "✓ Session set for student: {$sessionUserId}\n";
        echo "✓ User type: {$sessionUserType}\n";
        echo "✓ Session is logged in: " . (SessionManager::isLoggedIn() ? 'Yes' : 'No') . "\n";
    } else {
        echo "✗ Cannot test session without student data\n";
    }
    echo "\n";

    // 6. Test Quiz Attempt Creation
    echo "6. Testing Quiz Attempt Creation...\n";
    if ($sampleQuiz && $sampleStudent) {
        // Check for existing attempts
        $existingAttempt = QuizAttempt::where('quiz_id', $sampleQuiz->quiz_id)
            ->where('student_id', $sampleStudent->student_id)
            ->where('status', 'in_progress')
            ->first();
        
        if ($existingAttempt) {
            echo "✓ Found existing in-progress attempt: {$existingAttempt->attempt_id}\n";
            $testAttemptId = $existingAttempt->attempt_id;
        } else {
            // Create new attempt
            $newAttempt = QuizAttempt::create([
                'quiz_id' => $sampleQuiz->quiz_id,
                'student_id' => $sampleStudent->student_id,
                'started_at' => now(),
                'status' => 'in_progress',
                'answers' => [],
                'total_questions' => $sampleQuiz->questions->count()
            ]);
            echo "✓ Created new quiz attempt: {$newAttempt->attempt_id}\n";
            $testAttemptId = $newAttempt->attempt_id;
        }
    } else {
        echo "✗ Cannot create quiz attempt without quiz and student data\n";
        $testAttemptId = null;
    }
    echo "\n";

    // 7. Test Controller Logic
    echo "7. Testing Controller Logic...\n";
    if ($testAttemptId) {
        // Simulate the takeQuiz method logic
        $userId = SessionManager::get('user_id');
        echo "  - Session user_id: " . ($userId ?: 'NULL') . "\n";
        
        $student = Student::where('user_id', $userId)->first();
        echo "  - Student found: " . ($student ? $student->student_id : 'NULL') . "\n";
        
        $attempt = QuizAttempt::with(['quiz.questions', 'student'])
            ->find($testAttemptId);
        echo "  - Attempt found: " . ($attempt ? $attempt->attempt_id : 'NULL') . "\n";
        
        if ($attempt) {
            echo "  - Attempt status: {$attempt->status}\n";
            echo "  - Attempt student_id: {$attempt->student_id}\n";
            echo "  - Current student_id: " . ($student ? $student->student_id : 'NULL') . "\n";
            
            // Check ownership
            if ($student && $attempt->student_id === $student->student_id) {
                echo "  - ✓ Ownership verified\n";
            } else {
                echo "  - ✗ Ownership mismatch\n";
            }
            
            // Check status
            if ($attempt->status === 'in_progress') {
                echo "  - ✓ Status is in_progress\n";
            } else {
                echo "  - ✗ Status is not in_progress: {$attempt->status}\n";
            }
        }
    }
    echo "\n";

    // 8. Test Route Generation
    echo "8. Testing Route Generation...\n";
    if ($testAttemptId) {
        $route = route('student.quiz.take', $testAttemptId);
        echo "✓ Generated route: {$route}\n";
        
        // Test if route exists
        $routes = app('router')->getRoutes();
        $routeExists = false;
        foreach ($routes as $routeObj) {
            if ($routeObj->getName() === 'student.quiz.take') {
                $routeExists = true;
                break;
            }
        }
        echo "✓ Route 'student.quiz.take' exists: " . ($routeExists ? 'Yes' : 'No') . "\n";
    }
    echo "\n";

    // 9. Test View Existence
    echo "9. Testing View Existence...\n";
    $viewPath = resource_path('views/student/quiz/take.blade.php');
    if (file_exists($viewPath)) {
        echo "✓ View file exists: {$viewPath}\n";
        $viewSize = filesize($viewPath);
        echo "✓ View file size: {$viewSize} bytes\n";
    } else {
        echo "✗ View file not found: {$viewPath}\n";
    }
    echo "\n";

    // 10. Test Middleware
    echo "10. Testing Middleware...\n";
    $middlewareGroups = app('router')->getMiddlewareGroups();
    $routeMiddleware = app('router')->getMiddleware();
    
    echo "✓ Web middleware group exists: " . (isset($middlewareGroups['web']) ? 'Yes' : 'No') . "\n";
    echo "✓ 'check.session' middleware exists: " . (isset($routeMiddleware['check.session']) ? 'Yes' : 'No') . "\n";
    echo "✓ 'role.dashboard' middleware exists: " . (isset($routeMiddleware['role.dashboard']) ? 'Yes' : 'No') . "\n";
    echo "\n";

    // 11. Test Authentication Flow
    echo "11. Testing Authentication Flow...\n";
    if ($sampleStudent) {
        $user = User::find($sampleStudent->user_id);
        if ($user) {
            echo "✓ User found: {$user->email}\n";
            echo "✓ User role: {$user->role}\n";
        } else {
            echo "✗ User not found for student\n";
        }
    }
    echo "\n";

    // 12. Generate Test Recommendations
    echo "12. Test Recommendations:\n";
    echo "To test the quiz system manually:\n";
    echo "1. Login as a student\n";
    echo "2. Navigate to a quiz\n";
    echo "3. Click 'Start Quiz'\n";
    echo "4. Check browser network tab for redirects\n";
    echo "5. Check Laravel logs for errors\n";
    echo "\n";

    // 13. Check Laravel Logs
    echo "13. Checking Recent Laravel Logs...\n";
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $quizLogs = [];
        $lines = explode("\n", $logContent);
        $recentLines = array_slice($lines, -50); // Last 50 lines
        
        foreach ($recentLines as $line) {
            if (strpos($line, 'quiz') !== false || strpos($line, 'Quiz') !== false) {
                $quizLogs[] = $line;
            }
        }
        
        if (!empty($quizLogs)) {
            echo "✓ Found quiz-related log entries:\n";
            foreach (array_slice($quizLogs, -5) as $log) {
                echo "  - " . trim($log) . "\n";
            }
        } else {
            echo "✓ No recent quiz-related log entries found\n";
        }
    } else {
        echo "✗ Laravel log file not found\n";
    }
    echo "\n";

    echo "=== DEBUG COMPLETE ===\n";
    echo "If the quiz is still redirecting to dashboard, check:\n";
    echo "1. Browser console for JavaScript errors\n";
    echo "2. Network tab for failed requests\n";
    echo "3. Laravel logs for PHP errors\n";
    echo "4. Session data consistency\n";
    echo "5. Database constraints and foreign keys\n";

} catch (Exception $e) {
    echo "✗ Error during debugging: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} 