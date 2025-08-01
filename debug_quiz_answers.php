<?php
/**
 * Debug script to check quiz answer submission and storage
 */

// Bootstrap the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Set attempt ID from the URL
$attemptId = 13; // This is the attempt ID from the URL in your request

// Get the attempt data
$attempt = \App\Models\QuizAttempt::with(['quiz'])->find($attemptId);

if (!$attempt) {
    echo "Attempt not found!\n";
    exit;
}

echo "=== Quiz Attempt Information ===\n";
echo "Attempt ID: " . $attempt->attempt_id . "\n";
echo "Quiz ID: " . $attempt->quiz_id . "\n";
echo "Quiz Title: " . $attempt->quiz->quiz_title . "\n";
echo "Student ID: " . $attempt->student_id . "\n";
echo "Status: " . $attempt->status . "\n";
echo "Created at: " . $attempt->created_at . "\n";
echo "Updated at: " . $attempt->updated_at . "\n";
echo "IP Address: " . $attempt->ip_address . "\n";
echo "\n";

// Get answers for this attempt from the answers field
echo "=== Answers Stored ===\n";
$answers = $attempt->answers; // Already an array due to the cast in the model

// Dump the raw answers data for inspection
echo "Raw answers data: " . print_r($answers, true) . "\n\n";

// Get quiz questions for this quiz
$questions = \App\Models\QuizQuestion::where('quiz_id', $attempt->quiz_id)->get();
echo "Quiz has " . $questions->count() . " questions.\n\n";

// Display questions and answer info
if ($questions->count() > 0) {
    foreach ($questions as $i => $question) {
        echo "Question " . ($i + 1) . " (ID: " . $question->question_id . "): " . substr($question->question_text, 0, 50) . "...\n";
        echo "Type: " . $question->question_type . "\n";
        
        if ($question->question_type === 'multiple_choice') {
            $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
            echo "Options: " . print_r($options, true) . "\n";
            echo "Correct Answer: " . $question->correct_answer . "\n";
        }
        
        // Find user's answer in the attempt
        $userAnswer = "No answer recorded";
        $isCorrect = "No";
        
        if ($answers && is_array($answers)) {
            foreach ($answers as $answer) {
                if (isset($answer) && is_scalar($answer)) {
                    $userAnswer = $answer;
                    $isCorrect = ($answer === $question->correct_answer) ? "Yes" : "No";
                    break;
                }
            }
        }
        
        echo "User Answer: " . $userAnswer . "\n";
        echo "Is Correct: " . $isCorrect . "\n\n";
    }
} else {
    echo "No questions found for this quiz.\n";
}

// Check the database schema for the quiz attempts table
echo "=== Quiz Attempt Table Structure ===\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('quiz_attempts');
echo implode(", ", $columns) . "\n\n";

// Check the latest quiz attempts
echo "=== Latest 5 Quiz Attempts in Database ===\n";
$latestAttempts = \App\Models\QuizAttempt::orderBy('created_at', 'desc')->limit(5)->get();
foreach ($latestAttempts as $attempt) {
    echo "ID: " . $attempt->attempt_id . ", ";
    echo "Quiz ID: " . $attempt->quiz_id . ", ";
    echo "Student ID: " . $attempt->student_id . ", ";
    echo "Status: " . $attempt->status . ", ";
    echo "Created: " . $attempt->created_at . "\n";
}
