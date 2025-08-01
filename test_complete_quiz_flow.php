<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;

echo "=== COMPLETE QUIZ FLOW TEST ===" . PHP_EOL;

// Set up session
session([
    'user_id' => 1,
    'user_role' => 'student',
    'user_name' => 'Vince Michael Dela Vega',
    'user_firstname' => 'Vince Michael',
    'user_lastname' => 'Dela Vega',
    'user_email' => 'vince03handsome11@gmail.com'
]);

$student = Student::where('user_id', 1)->first();
$quiz = Quiz::with('questions')->find(44);

if (!$student || !$quiz) {
    echo "❌ Student or Quiz not found!" . PHP_EOL;
    exit;
}

echo "✅ Found student: " . $student->firstname . " " . $student->lastname . PHP_EOL;
echo "✅ Found quiz: " . $quiz->quiz_title . " (" . $quiz->questions->count() . " questions)" . PHP_EOL;

// 1. Create a fresh attempt
echo PHP_EOL . "1. CREATING FRESH QUIZ ATTEMPT" . PHP_EOL;

// Clean up any existing attempts for this test
QuizAttempt::where('quiz_id', $quiz->quiz_id)
    ->where('student_id', $student->student_id)
    ->where('status', 'in_progress')
    ->delete();

$attempt = QuizAttempt::create([
    'quiz_id' => $quiz->quiz_id,
    'student_id' => $student->student_id,
    'answers' => [],
    'score' => 0,
    'total_questions' => $quiz->questions->count(),
    'correct_answers' => 0,
    'started_at' => now(),
    'status' => 'in_progress'
]);

echo "✅ Created attempt: " . $attempt->attempt_id . PHP_EOL;

// 2. Test take quiz route
echo PHP_EOL . "2. TESTING TAKE QUIZ ROUTE" . PHP_EOL;

try {
    $takeRoute = route('student.quiz.take', ['attemptId' => $attempt->attempt_id]);
    echo "✅ Take route: " . $takeRoute . PHP_EOL;
} catch (\Exception $e) {
    echo "❌ Take route failed: " . $e->getMessage() . PHP_EOL;
}

// 3. Test submission route
echo PHP_EOL . "3. TESTING SUBMISSION ROUTE" . PHP_EOL;

try {
    $submitRoute = route('student.quiz.submit', ['attemptId' => $attempt->attempt_id]);
    echo "✅ Submit route: " . $submitRoute . PHP_EOL;
} catch (\Exception $e) {
    echo "❌ Submit route failed: " . $e->getMessage() . PHP_EOL;
}

// 4. Test submission with controller
echo PHP_EOL . "4. TESTING SUBMISSION CONTROLLER" . PHP_EOL;

use App\Http\Controllers\StudentDashboardController;
use Illuminate\Http\Request;

$controller = new StudentDashboardController();
$request = new Request();

// Add some sample answers
$sampleAnswers = [];
foreach ($quiz->questions as $question) {
    $sampleAnswers[$question->id] = 'A'; // Just pick A for all questions
}

$request->merge(['answers' => $sampleAnswers]);

try {
    $response = $controller->submitQuizAttempt($request, $attempt->attempt_id);
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        echo "✅ Submission successful!" . PHP_EOL;
        echo "  - Status Code: " . $response->status() . PHP_EOL;
        echo "  - Success: " . ($data['success'] ? 'Yes' : 'No') . PHP_EOL;
        echo "  - Score: " . ($data['score'] ?? 'N/A') . "%" . PHP_EOL;
        echo "  - Redirect: " . ($data['redirect'] ?? 'None') . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "❌ Submission failed: " . $e->getMessage() . PHP_EOL;
}

// 5. Test results route
echo PHP_EOL . "5. TESTING RESULTS ROUTE" . PHP_EOL;

try {
    $resultsRoute = route('student.quiz.results', ['attemptId' => $attempt->attempt_id]);
    echo "✅ Results route: " . $resultsRoute . PHP_EOL;
    
    // Test controller results method
    $resultsResponse = $controller->showQuizResults($attempt->attempt_id);
    if ($resultsResponse instanceof \Illuminate\View\View) {
        echo "✅ Results controller works" . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "❌ Results test failed: " . $e->getMessage() . PHP_EOL;
}

// 6. Test double submission prevention
echo PHP_EOL . "6. TESTING DOUBLE SUBMISSION PREVENTION" . PHP_EOL;

try {
    $request2 = new Request();
    $request2->merge(['answers' => $sampleAnswers]);
    $response2 = $controller->submitQuizAttempt($request2, $attempt->attempt_id);
    
    if ($response2 instanceof \Illuminate\Http\JsonResponse) {
        $data2 = $response2->getData(true);
        echo "Response to double submission:" . PHP_EOL;
        echo "  - Status Code: " . $response2->status() . PHP_EOL;
        echo "  - Success: " . ($data2['success'] ? 'Yes' : 'No') . PHP_EOL;
        echo "  - Message: " . ($data2['message'] ?? 'None') . PHP_EOL;
        
        if ($response2->status() === 400) {
            echo "✅ Double submission correctly prevented" . PHP_EOL;
        }
    }
} catch (\Exception $e) {
    echo "❌ Double submission test failed: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== QUIZ FLOW TEST COMPLETE ===" . PHP_EOL;
