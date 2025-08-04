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
use App\Models\User;
use App\Helpers\SessionManager;

echo "<h2>🧪 Complete Login & Quiz Flow Test</h2>\n";

// 1. Find a student to test with
echo "<h3>1. Finding Test Student</h3>\n";
$student = Student::where('student_id', '2025-08-00003')->first();

if (!$student) {
    echo "❌ Student 2025-08-00003 not found<br>\n";
    exit;
}

echo "✓ Found student: {$student->first_name} {$student->last_name}<br>\n";
echo "Student ID: {$student->student_id}<br>\n";
echo "User ID: {$student->user_id}<br>\n";

// 2. Find the corresponding user record
$user = User::find($student->user_id);
if (!$user) {
    echo "❌ User record not found for user_id: {$student->user_id}<br>\n";
    exit;
}

echo "✓ Found user: {$user->user_firstname} {$user->user_lastname}<br>\n";
echo "User email: {$user->email}<br>\n";

// 3. Simulate the login process (exactly as UnifiedLoginController does)
echo "<h3>2. Simulating Login Process</h3>\n";

// Set Laravel session (as done in loginStudent method)
session([
    'user_id' => $user->user_id,
    'user_name' => $user->user_firstname . ' ' . $user->user_lastname,
    'user_firstname' => $user->user_firstname,
    'user_lastname' => $user->user_lastname,
    'user_email' => $user->email,
    'user_role' => 'student',
    'role' => 'student',
    'logged_in' => true
]);

// Set SessionManager variables (as we just added to loginStudent method)
SessionManager::init();
SessionManager::set('user_id', $user->user_id);
SessionManager::set('user_name', $user->user_firstname . ' ' . $user->user_lastname);
SessionManager::set('user_firstname', $user->user_firstname);
SessionManager::set('user_lastname', $user->user_lastname);
SessionManager::set('user_email', $user->email);
SessionManager::set('user_role', 'student');
SessionManager::set('user_type', 'student');

echo "✓ Login simulation completed<br>\n";
echo "- Laravel user_id: " . session('user_id') . "<br>\n";
echo "- SessionManager user_id: " . SessionManager::get('user_id') . "<br>\n";
echo "- SessionManager isLoggedIn(): " . (SessionManager::isLoggedIn() ? 'YES' : 'NO') . "<br>\n";
echo "- SessionManager getUserType(): " . SessionManager::getUserType() . "<br>\n";

// 4. Test middleware simulation
echo "<h3>3. Testing Middleware Logic</h3>\n";

// Simulate CheckSession middleware
if (!SessionManager::isLoggedIn()) {
    echo "❌ SessionManager::isLoggedIn() returned false<br>\n";
    echo "This would redirect to homepage with 'Please log in to access this page.'<br>\n";
} else {
    echo "✓ SessionManager::isLoggedIn() returned true<br>\n";
    
    $userType = SessionManager::getUserType();
    if ($userType !== 'student') {
        echo "❌ User type is not 'student': {$userType}<br>\n";
        echo "This would cause access denied<br>\n";
    } else {
        echo "✓ User type is 'student'<br>\n";
        echo "✓ Middleware would allow access<br>\n";
    }
}

// 5. Find or create a quiz attempt
echo "<h3>4. Quiz Attempt Setup</h3>\n";

$existingAttempt = QuizAttempt::where('student_id', $student->student_id)
    ->where('status', 'in_progress')
    ->first();

if ($existingAttempt) {
    echo "✓ Using existing attempt: {$existingAttempt->attempt_id}<br>\n";
    $attemptId = $existingAttempt->attempt_id;
} else {
    // Create a new attempt
    $quiz = Quiz::where('is_active', true)->first();
    if (!$quiz) {
        echo "❌ No published quiz found<br>\n";
        exit;
    }
    
    $newAttempt = QuizAttempt::create([
        'quiz_id' => $quiz->quiz_id,
        'student_id' => $student->student_id,
        'status' => 'in_progress',
        'started_at' => now(),
        'score' => null,
        'total_questions' => $quiz->questions()->count(),
        'correct_answers' => 0
    ]);
    
    echo "✓ Created new attempt: {$newAttempt->attempt_id}<br>\n";
    $attemptId = $newAttempt->attempt_id;
}

// 6. Test the takeQuiz method
echo "<h3>5. Testing takeQuiz Method</h3>\n";

try {
    $controller = new \App\Http\Controllers\StudentDashboardController();
    $response = $controller->takeQuiz($attemptId);
    
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "❌ Method returned redirect: " . $response->getTargetUrl() . "<br>\n";
        echo "With message: " . session('error') . "<br>\n";
    } else {
        echo "✓ Method returned view successfully<br>\n";
        echo "Response type: " . get_class($response) . "<br>\n";
        
        // Check if it's a view
        if ($response instanceof \Illuminate\View\View) {
            echo "✓ View name: " . $response->getName() . "<br>\n";
            $viewData = $response->getData();
            echo "✓ View data keys: " . implode(', ', array_keys($viewData)) . "<br>\n";
            
            // Check if required data is present
            if (isset($viewData['attempt'])) {
                echo "✓ Attempt data present<br>\n";
            }
            if (isset($viewData['quiz'])) {
                echo "✓ Quiz data present<br>\n";
            }
            if (isset($viewData['questions'])) {
                echo "✓ Questions data present<br>\n";
            }
            if (isset($viewData['student'])) {
                echo "✓ Student data present<br>\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>\n";
}

// 7. Test route access
echo "<h3>6. Route Access Test</h3>\n";

$testUrl = "/student/quiz/take/{$attemptId}";
echo "Test URL: {$testUrl}<br>\n";
echo "<a href='{$testUrl}' target='_blank' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; font-weight: bold;'>🚀 Test Quiz Route Now</a><br>\n";

// 8. Test startQuiz method
echo "<h3>7. Testing startQuiz Method</h3>\n";

$quiz = Quiz::where('is_active', true)->first();
if ($quiz) {
    try {
        // Create a mock request
        $request = new \Illuminate\Http\Request();
        $request->merge(['quiz_id' => $quiz->quiz_id]);
        
        $response = $controller->startQuiz($quiz->quiz_id);
        
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = json_decode($response->getContent(), true);
            echo "✓ startQuiz returned JSON response<br>\n";
            echo "Success: " . ($data['success'] ?? 'unknown') . "<br>\n";
            if (isset($data['redirect'])) {
                echo "Redirect URL: {$data['redirect']}<br>\n";
            }
        } else {
            echo "❌ startQuiz returned unexpected response type: " . get_class($response) . "<br>\n";
        }
    } catch (\Exception $e) {
        echo "❌ startQuiz Exception: " . $e->getMessage() . "<br>\n";
    }
} else {
    echo "❌ No published quiz found for startQuiz test<br>\n";
}

// 9. Final verification
echo "<h3>8. Final Verification</h3>\n";

$laravelUserId = session('user_id');
$smUserId = SessionManager::get('user_id');
$laravelUserRole = session('user_role');
$smUserRole = SessionManager::get('user_role');

echo "Final Session State:<br>\n";
echo "- Laravel user_id: {$laravelUserId}<br>\n";
echo "- SessionManager user_id: {$smUserId}<br>\n";
echo "- Laravel user_role: {$laravelUserRole}<br>\n";
echo "- SessionManager user_role: {$smUserRole}<br>\n";
echo "- SessionManager isLoggedIn(): " . (SessionManager::isLoggedIn() ? 'YES' : 'NO') . "<br>\n";
echo "- SessionManager getUserType(): " . SessionManager::getUserType() . "<br>\n";

if ($laravelUserId === $smUserId && $laravelUserRole === $smUserRole && SessionManager::isLoggedIn()) {
    echo "✓ All session checks passed!<br>\n";
    echo "✅ The quiz system should now work correctly!<br>\n";
} else {
    echo "❌ Session inconsistency detected<br>\n";
}

echo "<hr>\n";
echo "<p><em>Complete test finished at: " . date('Y-m-d H:i:s') . "</em></p>\n";
?> 