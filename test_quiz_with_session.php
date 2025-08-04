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

echo "<h2>🧪 Quiz Access Test with Session</h2>\n";

// 1. Find a student to test with
echo "<h3>1. Finding Test Student</h3>\n";
$student = Student::where('student_id', '2025-08-00003')->first();

if (!$student) {
    echo "❌ Student 2025-08-00003 not found<br>\n";
    exit;
}

echo "✓ Found student: {$student->first_name} {$student->last_name}<br>\n";
echo "User ID: {$student->user_id}<br>\n";

// 2. Set up session data
echo "<h3>2. Setting Up Session</h3>\n";

// Set Laravel session
session([
    'user_id' => $student->user_id,
    'user_role' => 'student',
    'user_type' => 'student',
    'user_name' => $student->first_name . ' ' . $student->last_name,
    'user_firstname' => $student->first_name,
    'user_lastname' => $student->last_name,
    'user_email' => $student->email ?? 'test@example.com'
]);

// Set SessionManager data
SessionManager::init();
SessionManager::set('user_id', $student->user_id);
SessionManager::set('user_role', 'student');
SessionManager::set('user_type', 'student');
SessionManager::set('user_name', $student->first_name . ' ' . $student->last_name);
SessionManager::set('user_firstname', $student->first_name);
SessionManager::set('user_lastname', $student->last_name);
SessionManager::set('user_email', $student->email ?? 'test@example.com');

echo "✓ Session data set<br>\n";
echo "- Laravel user_id: " . session('user_id') . "<br>\n";
echo "- SessionManager user_id: " . SessionManager::get('user_id') . "<br>\n";
echo "- SessionManager isLoggedIn(): " . (SessionManager::isLoggedIn() ? 'YES' : 'NO') . "<br>\n";
echo "- SessionManager getUserType(): " . SessionManager::getUserType() . "<br>\n";

// 3. Find or create a quiz attempt
echo "<h3>3. Quiz Attempt Setup</h3>\n";

$existingAttempt = QuizAttempt::where('student_id', $student->student_id)
    ->where('status', 'in_progress')
    ->first();

if ($existingAttempt) {
    echo "✓ Using existing attempt: {$existingAttempt->attempt_id}<br>\n";
    $attemptId = $existingAttempt->attempt_id;
} else {
    // Create a new attempt
    $quiz = Quiz::where('is_published', true)->first();
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

// 4. Test the takeQuiz method directly
echo "<h3>4. Testing takeQuiz Method</h3>\n";

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
            echo "✓ View data keys: " . implode(', ', array_keys($response->getData())) . "<br>\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>\n";
}

// 5. Test route access
echo "<h3>5. Route Access Test</h3>\n";

$testUrl = "/student/quiz/take/{$attemptId}";
echo "Test URL: {$testUrl}<br>\n";
echo "<a href='{$testUrl}' target='_blank' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; font-weight: bold;'>🚀 Test Quiz Route Now</a><br>\n";

// 6. Verify session consistency
echo "<h3>6. Session Consistency Check</h3>\n";

$laravelUserId = session('user_id');
$smUserId = SessionManager::get('user_id');
$laravelUserRole = session('user_role');
$smUserRole = SessionManager::get('user_role');

echo "Session Consistency:<br>\n";
echo "- Laravel user_id: {$laravelUserId}<br>\n";
echo "- SessionManager user_id: {$smUserId}<br>\n";
echo "- Laravel user_role: {$laravelUserRole}<br>\n";
echo "- SessionManager user_role: {$smUserRole}<br>\n";

if ($laravelUserId === $smUserId && $laravelUserRole === $smUserRole) {
    echo "✓ Sessions are consistent<br>\n";
} else {
    echo "❌ Sessions are inconsistent<br>\n";
}

// 7. Test middleware simulation
echo "<h3>7. Middleware Simulation</h3>\n";

// Simulate CheckSession middleware
if (!SessionManager::isLoggedIn()) {
    echo "❌ SessionManager::isLoggedIn() returned false<br>\n";
} else {
    echo "✓ SessionManager::isLoggedIn() returned true<br>\n";
    
    $userType = SessionManager::getUserType();
    if ($userType !== 'student') {
        echo "❌ User type is not 'student': {$userType}<br>\n";
    } else {
        echo "✓ User type is 'student'<br>\n";
    }
}

echo "<hr>\n";
echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>\n";
?> 