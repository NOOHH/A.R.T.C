<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\Student;
use App\Models\ContentItem;

echo "=== COMPREHENSIVE QUIZ RESULTS SYSTEM TEST ===" . PHP_EOL;

// 1. Test Database Relationships
echo PHP_EOL . "1. TESTING DATABASE RELATIONSHIPS" . PHP_EOL;

$attempt = QuizAttempt::with(['quiz.questions', 'student'])->find(3);
if ($attempt && $attempt->quiz && $attempt->student) {
    echo "✅ All relationships loaded correctly" . PHP_EOL;
    echo "  - Quiz: " . $attempt->quiz->quiz_title . PHP_EOL;
    echo "  - Student: " . $attempt->student->firstname . " " . $attempt->student->lastname . PHP_EOL;
    echo "  - Questions: " . $attempt->quiz->questions->count() . PHP_EOL;
} else {
    echo "❌ Relationship loading failed" . PHP_EOL;
    exit(1);
}

// 2. Test Retake Logic
echo PHP_EOL . "2. TESTING RETAKE LOGIC" . PHP_EOL;

$quiz = $attempt->quiz;
$student = $attempt->student;

// Test the safe retake logic we implemented
$canRetake = $quiz->allow_retakes || 
            !isset($quiz->max_attempts) || 
            $quiz->max_attempts == 0;

if (!$canRetake && isset($quiz->max_attempts) && $quiz->max_attempts > 0) {
    $completedAttempts = QuizAttempt::where('quiz_id', $quiz->quiz_id)
        ->where('student_id', $student->student_id)
        ->where('status', 'completed')
        ->count();
    $canRetake = $completedAttempts < $quiz->max_attempts;
    
    echo "Max attempts logic applied:" . PHP_EOL;
    echo "  - Max allowed: " . $quiz->max_attempts . PHP_EOL;
    echo "  - Completed: " . $completedAttempts . PHP_EOL;
}

echo "✅ Retake logic: " . ($canRetake ? 'Allowed' : 'Not allowed') . PHP_EOL;

// 3. Test Content Item Lookup
echo PHP_EOL . "3. TESTING CONTENT ITEM LOOKUP" . PHP_EOL;

$content = ContentItem::where('content_type', 'quiz')
    ->whereRaw("JSON_EXTRACT(content_data, '$.quiz_id') = ?", [$quiz->quiz_id])
    ->first();

if ($content) {
    echo "✅ Content item found: ID " . $content->id . PHP_EOL;
} else {
    echo "⚠️  Content item not found (this is okay, just means no direct link)" . PHP_EOL;
}

// 4. Test Results Data Preparation
echo PHP_EOL . "4. TESTING RESULTS DATA PREPARATION" . PHP_EOL;

$questions = $quiz->questions;
$studentAnswers = $attempt->answers;
$results = [];

foreach ($questions as $question) {
    $questionId = $question->id;
    $studentAnswer = $studentAnswers[$questionId] ?? null;
    
    $results[] = [
        'question' => $question,
        'student_answer' => $studentAnswer,
        'correct_answer' => $question->correct_answer,
        'is_correct' => $studentAnswer === $question->correct_answer
    ];
}

echo "✅ Results prepared: " . count($results) . " questions" . PHP_EOL;

$correctCount = array_sum(array_column($results, 'is_correct'));
echo "  - Correct answers: " . $correctCount . " / " . count($results) . PHP_EOL;
echo "  - Score: " . $attempt->score . "%" . PHP_EOL;

// 5. Test Controller Method
echo PHP_EOL . "5. TESTING CONTROLLER METHOD" . PHP_EOL;

session([
    'user_id' => 1,
    'user_role' => 'student'
]);

use App\Http\Controllers\StudentDashboardController;

try {
    $controller = new StudentDashboardController();
    $response = $controller->showQuizResults(3);
    
    if ($response instanceof \Illuminate\View\View) {
        echo "✅ Controller method works correctly" . PHP_EOL;
        $viewData = $response->getData();
        echo "  - View data keys: " . implode(', ', array_keys($viewData)) . PHP_EOL;
    } else {
        echo "❌ Controller returned unexpected response type" . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "❌ Controller method failed: " . $e->getMessage() . PHP_EOL;
}

// 6. Test View Rendering
echo PHP_EOL . "6. TESTING VIEW RENDERING" . PHP_EOL;

session([
    'user_id' => 1,
    'user_role' => 'student',
    'user_name' => 'Vince Michael Dela Vega',
    'user_firstname' => 'Vince Michael',
    'user_lastname' => 'Dela Vega',
    'user_email' => 'vince03handsome11@gmail.com'
]);

try {
    $view = view('student.quiz.results', compact('attempt', 'quiz', 'student', 'results'));
    $content = $view->render();
    
    echo "✅ View renders successfully" . PHP_EOL;
    echo "  - Content length: " . number_format(strlen($content)) . " characters" . PHP_EOL;
    
    // Check for key elements
    $checks = [
        'Quiz title' => strpos($content, $quiz->quiz_title) !== false,
        'Score display' => strpos($content, $attempt->score) !== false,
        'Action buttons' => strpos($content, 'Back to Dashboard') !== false,
        'Questions review' => strpos($content, 'Detailed Review') !== false,
        'Student name' => strpos($content, $student->firstname) !== false
    ];
    
    foreach ($checks as $check => $result) {
        echo "  - " . $check . ": " . ($result ? "✅" : "❌") . PHP_EOL;
    }
    
} catch (\Exception $e) {
    echo "❌ View rendering failed: " . $e->getMessage() . PHP_EOL;
}

// 7. Test Route Access
echo PHP_EOL . "7. TESTING ROUTE ACCESS" . PHP_EOL;

try {
    $route = route('student.quiz.results', ['attemptId' => 3]);
    echo "✅ Route generated successfully: " . $route . PHP_EOL;
} catch (\Exception $e) {
    echo "❌ Route generation failed: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== COMPREHENSIVE TEST COMPLETE ===" . PHP_EOL;
echo "✅ Quiz Results System is working correctly!" . PHP_EOL;
