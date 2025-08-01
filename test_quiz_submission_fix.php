<?php
/**
 * Test the fixed quiz submission process
 */

// Bootstrap the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING QUIZ SUBMISSION PROCESS ===\n\n";

// Create a new quiz attempt to test
$studentId = '2025-07-00001';
$quizId = 47;

// Create a new attempt
$newAttempt = \App\Models\QuizAttempt::create([
    'quiz_id' => $quizId,
    'student_id' => $studentId,
    'answers' => json_encode([]),
    'score' => 0,
    'total_questions' => 1,
    'correct_answers' => 0,
    'started_at' => now(),
    'status' => 'in_progress'
]);

echo "Created test attempt ID: " . $newAttempt->attempt_id . "\n";

// Simulate the submission process using the fixed controller logic
echo "\nTesting submission with answer 'A' (should convert to '0'):\n";

// Get the quiz and questions
$quiz = \App\Models\Quiz::with('questions')->find($quizId);
$questions = $quiz->questions;

// Simulate submitted answers (letter format)
$submittedAnswers = ['416' => 'A']; // User selected option A

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

// Test with index format answer too
echo "\n" . str_repeat("-", 50) . "\n";
echo "Testing submission with answer '0' (index format):\n";

// Create another test attempt
$newAttempt2 = \App\Models\QuizAttempt::create([
    'quiz_id' => $quizId,
    'student_id' => $studentId,
    'answers' => json_encode([]),
    'score' => 0,
    'total_questions' => 1,
    'correct_answers' => 0,
    'started_at' => now(),
    'status' => 'in_progress'
]);

echo "Created test attempt ID: " . $newAttempt2->attempt_id . "\n";

// Simulate submitted answers (index format)
$submittedAnswers2 = ['416' => '0']; // User selected option 0

$correctAnswers2 = 0;

foreach ($questions as $question) {
    $questionId = (string)$question->id;
    $studentAnswer = $submittedAnswers2[$questionId] ?? null;
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
            $correctAnswers2++;
        }
    }
}

$score2 = $totalQuestions > 0 ? ($correctAnswers2 / $totalQuestions) * 100 : 0;

echo "\nCalculated Score: $score2%\n";
echo "Correct Answers: $correctAnswers2 / $totalQuestions\n";

// Update the test attempt
$newAttempt2->update([
    'answers' => json_encode($submittedAnswers2),
    'score' => $score2,
    'correct_answers' => $correctAnswers2,
    'completed_at' => now(),
    'status' => 'completed'
]);

echo "\nTest attempt updated successfully!\n";
echo "Attempt ID " . $newAttempt2->attempt_id . " now has score: " . $newAttempt2->fresh()->score . "%\n";

echo "\n=== TEST COMPLETED - BOTH SHOULD SHOW 100% ===\n";
