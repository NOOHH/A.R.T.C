<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;

echo "=== TESTING QUIZ SUBMISSION SCENARIO ===" . PHP_EOL;

// Create a fresh quiz attempt for testing
$student = Student::where('user_id', 1)->first();
$quiz = Quiz::find(44); // Use the quiz from the logs

if (!$student || !$quiz) {
    echo "❌ Student or Quiz not found!" . PHP_EOL;
    exit;
}

// Check if there's an active attempt
$activeAttempt = QuizAttempt::where('quiz_id', $quiz->quiz_id)
    ->where('student_id', $student->student_id)
    ->where('status', 'in_progress')
    ->first();

if ($activeAttempt) {
    echo "✅ Found active attempt: " . $activeAttempt->attempt_id . PHP_EOL;
    $testAttemptId = $activeAttempt->attempt_id;
} else {
    echo "Creating new quiz attempt for testing..." . PHP_EOL;
    
    // Create new attempt
    $newAttempt = QuizAttempt::create([
        'quiz_id' => $quiz->quiz_id,
        'student_id' => $student->student_id,
        'answers' => [],
        'score' => 0,
        'total_questions' => $quiz->questions->count(),
        'correct_answers' => 0,
        'started_at' => now(),
        'status' => 'in_progress'
    ]);
    
    echo "✅ Created new attempt: " . $newAttempt->attempt_id . PHP_EOL;
    $testAttemptId = $newAttempt->attempt_id;
}

// Now test the submission logic
echo PHP_EOL . "=== TESTING SUBMISSION LOGIC ===" . PHP_EOL;

session([
    'user_id' => 1,
    'user_role' => 'student'
]);

// Simulate the controller submission
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Http\Request;

$controller = new StudentDashboardController();

// Create a mock request with sample answers
$request = new Request();
$request->merge([
    'answers' => [
        '413' => 'A' // Assuming question 413 exists
    ]
]);

try {
    $response = $controller->submitQuizAttempt($request, $testAttemptId);
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        echo "✅ Submission successful!" . PHP_EOL;
        echo "  - Success: " . ($data['success'] ? 'Yes' : 'No') . PHP_EOL;
        echo "  - Score: " . ($data['score'] ?? 'N/A') . PHP_EOL;
        echo "  - Message: " . ($data['message'] ?? 'None') . PHP_EOL;
        echo "  - Status Code: " . $response->status() . PHP_EOL;
        
        if (isset($data['redirect'])) {
            echo "  - Redirect: " . $data['redirect'] . PHP_EOL;
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Submission failed: " . $e->getMessage() . PHP_EOL;
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}

echo PHP_EOL . "=== TEST COMPLETE ===" . PHP_EOL;
