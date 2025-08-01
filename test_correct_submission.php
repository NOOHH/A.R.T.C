<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CREATING CORRECT QUIZ SUBMISSION TEST ===\n\n";

$studentId = '2025-07-00001';
$quizId = 47;

// Create a new attempt with the CORRECT answer
$newAttempt = \App\Models\QuizAttempt::create([
    'quiz_id' => $quizId,
    'student_id' => $studentId,
    'answers' => json_encode(['416' => 'A']), // Submit as letter format
    'score' => 0,
    'total_questions' => 1,
    'correct_answers' => 0,
    'started_at' => now(),
    'status' => 'in_progress'
]);

echo "Created test attempt ID: " . $newAttempt->attempt_id . "\n";
echo "Submitted answer: A (EEETPOOO - the correct answer)\n\n";

// Now simulate the controller scoring logic
$quiz = \App\Models\Quiz::with('questions')->find($quizId);
$questions = $quiz->questions;
$submittedAnswers = ['416' => 'A']; // Letter format

$correctAnswers = 0;
$totalQuestions = $questions->count();

foreach ($questions as $question) {
    $questionId = (string)$question->id;
    $studentAnswer = $submittedAnswers[$questionId] ?? null;
    $correctAnswer = $question->correct_answer;
    
    echo "Question ID: $questionId\n";
    echo "Student Answer: $studentAnswer\n";
    echo "Correct Answer: $correctAnswer\n";

    if ($studentAnswer !== null) {
        $isCorrect = false;
        
        if ($question->question_type === 'multiple_choice') {
            // Handle letter to index conversion if needed
            if (is_string($studentAnswer) && preg_match('/^[A-Z]$/', $studentAnswer)) {
                // Convert letter (A, B, C) to index (0, 1, 2)
                $convertedAnswer = (string)(ord($studentAnswer) - 65);
                echo "Converted '$studentAnswer' to '$convertedAnswer'\n";
                $isCorrect = $convertedAnswer === (string)$correctAnswer;
            } else {
                // Direct comparison (both should be strings)
                $isCorrect = (string)$studentAnswer === (string)$correctAnswer;
            }
        } else {
            // For other question types (true/false, etc.)
            $isCorrect = $studentAnswer === $correctAnswer;
        }
        
        echo "Is Correct: " . ($isCorrect ? "YES" : "NO") . "\n";
        
        if ($isCorrect) {
            $correctAnswers++;
        }
    }
}

$score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

echo "\nCalculated Score: $score%\n";
echo "Correct Answers: $correctAnswers / $totalQuestions\n";

// Update the test attempt
$newAttempt->update([
    'answers' => json_encode($submittedAnswers),
    'score' => $score,
    'correct_answers' => $correctAnswers,
    'completed_at' => now(),
    'status' => 'completed'
]);

echo "\nTest attempt updated successfully!\n";
echo "Attempt ID " . $newAttempt->attempt_id . " now has score: " . $newAttempt->fresh()->score . "%\n";
echo "\nYou can view the results at: http://127.0.0.1:8000/student/quiz/attempt/" . $newAttempt->attempt_id . "/results\n";

echo "\n=== TEST COMPLETED ===\n";
