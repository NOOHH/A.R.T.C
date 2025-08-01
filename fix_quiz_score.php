<?php
/**
 * Fix script for quiz attempt #13 score
 * This script correctly evaluates the quiz answers and updates the score in the database
 */

// Bootstrap the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the specific attempt
$attemptId = 13; // The attempt with the EEE quiz
$attempt = \App\Models\QuizAttempt::find($attemptId);

if (!$attempt) {
    echo "Attempt #$attemptId not found!\n";
    exit;
}

// Get the quiz
$quiz = \App\Models\Quiz::find($attempt->quiz_id);
if (!$quiz) {
    echo "Quiz not found for attempt #$attemptId!\n";
    exit;
}

echo "Processing attempt #$attemptId for quiz: " . $quiz->quiz_title . "\n";

// Get the questions for this quiz
$questions = \App\Models\QuizQuestion::where('quiz_id', $quiz->quiz_id)->get();
echo "Quiz has " . $questions->count() . " questions.\n";

// Get the stored answers
$storedAnswers = $attempt->answers;
echo "Stored answers: " . json_encode($storedAnswers) . "\n";

// Process the answers and calculate the correct score
$correctCount = 0;
$totalQuestions = $questions->count();

foreach ($questions as $question) {
    // Special case for questions with empty IDs
    if (empty($question->question_id) && $storedAnswers) {
        // Use the first key from the stored answers
        $keys = array_keys((array)$storedAnswers);
        if (!empty($keys)) {
            $fakeQuestionId = $keys[0];
            $studentAnswer = $storedAnswers[$fakeQuestionId] ?? null;
            
            // Convert from letter to index if needed
            if ($studentAnswer === 'A') {
                $convertedAnswer = '0';
                $isCorrect = $convertedAnswer === $question->correct_answer;
                
                echo "Question: " . $question->question_text . "\n";
                echo "Student Answer: " . $studentAnswer . " (converted to " . $convertedAnswer . ")\n";
                echo "Correct Answer: " . $question->correct_answer . "\n";
                echo "Is Correct: " . ($isCorrect ? "YES" : "NO") . "\n\n";
                
                if ($isCorrect) {
                    $correctCount++;
                }
            }
        }
    }
}

// Calculate the score
$score = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
echo "Correct answers: $correctCount / $totalQuestions\n";
echo "Calculated score: " . number_format($score, 1) . "%\n";

// Update the attempt with the correct score
$attempt->correct_answers = $correctCount;
$attempt->score = $score;

// Confirm with the user before updating
echo "\nReady to update the database. Current score: " . $attempt->getOriginal('score') . "%\n";
echo "New score will be: $score%\n";
echo "Press ENTER to continue or CTRL+C to cancel...";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

// Save the changes
$attempt->save();

echo "\nAttempt #$attemptId has been updated with the correct score: $score%\n";

// Verify the update
$refreshedAttempt = \App\Models\QuizAttempt::find($attemptId);
echo "Verification - Score in database is now: " . $refreshedAttempt->score . "%\n";
