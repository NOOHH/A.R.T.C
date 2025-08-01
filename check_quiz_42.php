<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Quiz;
use App\Models\Student;

$quiz = Quiz::with('questions')->find(42);
if ($quiz) {
    echo "Quiz ID: " . $quiz->quiz_id . PHP_EOL;
    echo "Quiz Title: " . $quiz->quiz_title . PHP_EOL;
    echo "Quiz Description: " . $quiz->quiz_description . PHP_EOL;
    echo "Quiz Status: " . $quiz->status . PHP_EOL;
    echo "Total Questions: " . $quiz->total_questions . PHP_EOL;
    echo "Time Limit: " . $quiz->time_limit . " minutes" . PHP_EOL;
    echo "Max Attempts: " . $quiz->max_attempts . PHP_EOL;
    echo "Questions Count: " . $quiz->questions->count() . PHP_EOL;
    
    echo "\nFirst few questions:" . PHP_EOL;
    foreach ($quiz->questions->take(3) as $question) {
        echo "- " . $question->question_text . PHP_EOL;
        echo "  Type: " . $question->question_type . PHP_EOL;
    }
} else {
    echo "Quiz with ID 42 not found" . PHP_EOL;
}

// Check if there are any student attempts
echo "\n=== Checking Quiz Attempts ===\n";
$attempts = \App\Models\QuizAttempt::where('quiz_id', 42)->get();
echo "Total attempts for quiz 42: " . $attempts->count() . PHP_EOL;
