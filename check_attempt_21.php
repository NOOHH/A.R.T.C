<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$attempt = \App\Models\QuizAttempt::find(21);
if($attempt) {
    echo "Attempt 21 Data:\n";
    echo "Quiz ID: " . $attempt->quiz_id . "\n";
    echo "Student ID: " . $attempt->student_id . "\n";
    echo "Score: " . $attempt->score . "%\n";
    echo "Answers: " . json_encode($attempt->answers) . "\n";
    echo "Status: " . $attempt->status . "\n";
    
    // Get the quiz and its questions
    $quiz = \App\Models\Quiz::with('questions')->find($attempt->quiz_id);
    if($quiz) {
        echo "\nQuiz: " . $quiz->quiz_title . "\n";
        echo "Questions:\n";
        foreach($quiz->questions as $question) {
            echo "- Question {$question->id}: {$question->question_text}\n";
            echo "  Options: " . $question->options . "\n";
            echo "  Correct: {$question->correct_answer}\n";
        }
    }
} else {
    echo "Attempt 21 not found\n";
}
