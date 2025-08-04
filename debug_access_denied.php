<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Student;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Helpers\SessionManager;

echo "<h2>🔍 Access Denied Debug Report</h2>\n";

// 1. Check current session state
echo "<h3>1. Session State Analysis</h3>\n";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>\n";

// Laravel session
$laravelUserId = session('user_id');
$laravelUserRole = session('user_role');
$laravelUserType = session('user_type');

echo "<strong>Laravel Session:</strong><br>\n";
echo "- user_id: " . ($laravelUserId ?? 'NULL') . "<br>\n";
echo "- user_role: " . ($laravelUserRole ?? 'NULL') . "<br>\n";
echo "- user_type: " . ($laravelUserType ?? 'NULL') . "<br>\n";

// SessionManager
SessionManager::init();
$smUserId = SessionManager::get('user_id');
$smUserType = SessionManager::get('user_type');
$smUserRole = SessionManager::get('user_role');

echo "<strong>SessionManager:</strong><br>\n";
echo "- user_id: " . ($smUserId ?? 'NULL') . "<br>\n";
echo "- user_type: " . ($smUserType ?? 'NULL') . "<br>\n";
echo "- user_role: " . ($smUserRole ?? 'NULL') . "<br>\n";

// Check if logged in
$isLoggedInLaravel = !empty($laravelUserId);
$isLoggedInSM = SessionManager::isLoggedIn();
$userTypeSM = SessionManager::getUserType();

echo "<strong>Authentication Status:</strong><br>\n";
echo "- Laravel session has user_id: " . ($isLoggedInLaravel ? 'YES' : 'NO') . "<br>\n";
echo "- SessionManager isLoggedIn(): " . ($isLoggedInSM ? 'YES' : 'NO') . "<br>\n";
echo "- SessionManager getUserType(): " . ($userTypeSM ?? 'NULL') . "<br>\n";

echo "</div>\n";

// 2. Check student record
echo "<h3>2. Student Record Check</h3>\n";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>\n";

if ($laravelUserId) {
    $student = Student::where('user_id', $laravelUserId)->first();
    if ($student) {
        echo "✓ Student found:<br>\n";
        echo "- Student ID: {$student->student_id}<br>\n";
        echo "- User ID: {$student->user_id}<br>\n";
        echo "- Name: {$student->first_name} {$student->last_name}<br>\n";
        echo "- Status: {$student->status}<br>\n";
    } else {
        echo "✗ No student record found for user_id: {$laravelUserId}<br>\n";
    }
} else {
    echo "✗ No user_id in session to check student record<br>\n";
}

echo "</div>\n";

// 3. Check recent quiz attempts
echo "<h3>3. Recent Quiz Attempts</h3>\n";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>\n";

$recentAttempts = QuizAttempt::with(['student', 'quiz'])
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get();

if ($recentAttempts->count() > 0) {
    echo "Recent quiz attempts:<br>\n";
    foreach ($recentAttempts as $attempt) {
        echo "- Attempt ID: {$attempt->attempt_id}<br>\n";
        echo "  Student ID: {$attempt->student_id}<br>\n";
        echo "  Quiz ID: {$attempt->quiz_id}<br>\n";
        echo "  Status: {$attempt->status}<br>\n";
        echo "  Created: {$attempt->created_at}<br>\n";
        echo "<br>\n";
    }
} else {
    echo "No quiz attempts found<br>\n";
}

echo "</div>\n";

// 4. Test middleware logic
echo "<h3>4. Middleware Logic Test</h3>\n";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>\n";

// Simulate CheckSession middleware logic
echo "<strong>CheckSession Middleware Simulation:</strong><br>\n";

if (!SessionManager::isLoggedIn()) {
    echo "✗ SessionManager::isLoggedIn() returned false<br>\n";
    echo "This would redirect to homepage with 'Please log in to access this page.'<br>\n";
} else {
    echo "✓ SessionManager::isLoggedIn() returned true<br>\n";
    
    $userType = SessionManager::getUserType();
    echo "User type: " . ($userType ?? 'NULL') . "<br>\n";
    
    if ($userType !== 'student') {
        echo "✗ User type is not 'student' - this would cause access denied<br>\n";
    } else {
        echo "✓ User type is 'student'<br>\n";
    }
}

echo "</div>\n";

// 5. Test takeQuiz method logic
echo "<h3>5. TakeQuiz Method Logic Test</h3>\n";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>\n";

if ($recentAttempts->count() > 0) {
    $testAttempt = $recentAttempts->first();
    echo "Testing with attempt ID: {$testAttempt->attempt_id}<br>\n";
    
    // Simulate takeQuiz logic
    $userId = session('user_id');
    if (!$userId) {
        echo "✗ No user_id in session - would redirect to dashboard<br>\n";
    } else {
        echo "✓ user_id found: {$userId}<br>\n";
        
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            echo "✗ Student not found for user_id: {$userId}<br>\n";
        } else {
            echo "✓ Student found: {$student->student_id}<br>\n";
            
            // Check ownership
            if ($testAttempt->student_id !== $student->student_id) {
                echo "✗ Ownership mismatch:<br>\n";
                echo "  - Attempt student_id: {$testAttempt->student_id}<br>\n";
                echo "  - Current student_id: {$student->student_id}<br>\n";
                echo "This would cause 'Access denied'<br>\n";
            } else {
                echo "✓ Ownership verified<br>\n";
            }
            
            // Check status
            if ($testAttempt->status !== 'in_progress') {
                echo "✗ Attempt status is not 'in_progress': {$testAttempt->status}<br>\n";
            } else {
                echo "✓ Attempt status is 'in_progress'<br>\n";
            }
        }
    }
} else {
    echo "No attempts available for testing<br>\n";
}

echo "</div>\n";

// 6. Check for any error messages in logs
echo "<h3>6. Recent Log Entries</h3>\n";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>\n";

$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -20); // Last 20 lines
    
    echo "Recent log entries:<br>\n";
    foreach ($recentLines as $line) {
        if (strpos($line, 'takeQuiz') !== false || 
            strpos($line, 'Access denied') !== false || 
            strpos($line, 'Student not found') !== false) {
            echo "<span style='color: #dc3545;'>" . htmlspecialchars($line) . "</span><br>\n";
        }
    }
} else {
    echo "Log file not found<br>\n";
}

echo "</div>\n";

// 7. Test route access
echo "<h3>7. Route Access Test</h3>\n";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>\n";

if ($recentAttempts->count() > 0) {
    $testAttemptId = $recentAttempts->first()->attempt_id;
    $testUrl = "/student/quiz/take/{$testAttemptId}";
    
    echo "Test URL: {$testUrl}<br>\n";
    echo "<a href='{$testUrl}' target='_blank' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Test Quiz Route</a><br>\n";
} else {
    echo "No attempts available for route testing<br>\n";
}

echo "</div>\n";

// 8. Recommendations
echo "<h3>8. Recommendations</h3>\n";
echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #007bff;'>\n";

if (!$isLoggedInLaravel) {
    echo "🔴 <strong>Primary Issue:</strong> No user_id in Laravel session<br>\n";
    echo "   - Check login process<br>\n";
    echo "   - Verify session configuration<br>\n";
} elseif ($userTypeSM !== 'student') {
    echo "🔴 <strong>Primary Issue:</strong> User type is not 'student'<br>\n";
    echo "   - Current type: " . ($userTypeSM ?? 'NULL') . "<br>\n";
    echo "   - Check user role assignment during login<br>\n";
} else {
    echo "🟡 <strong>Potential Issue:</strong> Session inconsistency between Laravel and SessionManager<br>\n";
    echo "   - Laravel session and SessionManager may have different values<br>\n";
    echo "   - Check if both are being updated consistently during login<br>\n";
}

echo "</div>\n";

echo "<hr>\n";
echo "<p><em>Debug completed at: " . date('Y-m-d H:i:s') . "</em></p>\n";
?> 