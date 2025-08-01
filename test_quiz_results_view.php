<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QuizAttempt;
use App\Models\Student;

echo "=== TESTING QUIZ RESULTS VIEW RENDERING ===" . PHP_EOL;

// Set up proper session data
session([
    'user_id' => 1,
    'user_role' => 'student', 
    'user_name' => 'Vince Michael Dela Vega',
    'user_firstname' => 'Vince Michael',
    'user_lastname' => 'Dela Vega',
    'user_email' => 'vince03handsome11@gmail.com'
]);

// Get the data
$student = Student::where('user_id', 1)->first();
$attempt = QuizAttempt::with(['quiz.questions', 'student'])->find(3);
$quiz = $attempt->quiz;
$questions = $quiz->questions;
$studentAnswers = $attempt->answers;

// Prepare detailed results
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

try {
    // Try to render the view
    $view = view('student.quiz.results', compact(
        'attempt',
        'quiz', 
        'student',
        'results'
    ));
    
    // Get the rendered content (this will trigger any PHP errors)
    $content = $view->render();
    
    echo "✅ View rendered successfully!" . PHP_EOL;
    echo "  - Content length: " . strlen($content) . " characters" . PHP_EOL;
    
    // Check if the view contains the expected elements
    if (strpos($content, 'TEST') !== false) {
        echo "✅ Quiz title found in rendered content" . PHP_EOL;
    }
    
    if (strpos($content, 'Action Buttons') !== false || strpos($content, 'Back to Dashboard') !== false) {
        echo "✅ Action buttons found in rendered content" . PHP_EOL;
    }
    
    if (strpos($content, 'Detailed Review') !== false) {
        echo "✅ Questions review section found in rendered content" . PHP_EOL;
    }
    
} catch (\Exception $e) {
    echo "❌ View rendering failed with error:" . PHP_EOL;
    echo "  " . $e->getMessage() . PHP_EOL;
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    
    if ($e->getPrevious()) {
        echo "  Previous: " . $e->getPrevious()->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "=== TEST COMPLETE ===" . PHP_EOL;
