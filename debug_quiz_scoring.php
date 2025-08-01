<?php
/**
 * Debug Quiz Scoring Issue
 * This script will debug why the quiz is showing 0% when there are correct answers
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== DEBUGGING QUIZ SCORING ISSUE ===\n\n";

try {
    // Find the most recent quiz attempt
    $latestAttempt = DB::table('quiz_attempts')
        ->orderBy('completed_at', 'desc')
        ->first();

    if (!$latestAttempt) {
        throw new Exception("No quiz attempts found");
    }

    echo "Latest Quiz Attempt Details:\n";
    echo "- Attempt ID: {$latestAttempt->attempt_id}\n";
    echo "- Quiz ID: {$latestAttempt->quiz_id}\n";
    echo "- Student ID: {$latestAttempt->student_id}\n";
    echo "- Score: {$latestAttempt->score}\n";
    echo "- Correct Answers: {$latestAttempt->correct_answers}\n";
    echo "- Total Questions: {$latestAttempt->total_questions}\n";
    echo "- Status: {$latestAttempt->status}\n";
    echo "- Completed At: {$latestAttempt->completed_at}\n\n";

    // Get the quiz details
    $quiz = DB::table('quizzes')->where('quiz_id', $latestAttempt->quiz_id)->first();
    echo "Quiz Details:\n";
    echo "- Quiz Title: {$quiz->quiz_title}\n";
    echo "- Quiz ID: {$quiz->quiz_id}\n\n";

    // Get the questions for this quiz
    $questions = DB::table('quiz_questions')
        ->where('quiz_id', $latestAttempt->quiz_id)
        ->where('is_active', true)
        ->orderBy('question_order')
        ->get();

    echo "Quiz Questions (" . count($questions) . " total):\n";
    foreach ($questions as $index => $question) {
        echo "Question " . ($index + 1) . " (ID: {$question->id}):\n";
        echo "  Text: " . substr($question->question_text, 0, 80) . "...\n";
        echo "  Type: {$question->question_type}\n";
        echo "  Correct Answer: '{$question->correct_answer}'\n";
        
        if ($question->options) {
            $options = json_decode($question->options, true);
            if ($options) {
                echo "  Options:\n";
                foreach ($options as $optIndex => $option) {
                    echo "    " . chr(65 + $optIndex) . ". {$option}\n";
                }
            }
        }
        echo "\n";
    }

    // Parse submitted answers
    $submittedAnswers = json_decode($latestAttempt->answers, true);
    echo "Submitted Answers:\n";
    echo "Raw answers data: " . $latestAttempt->answers . "\n\n";

    if ($submittedAnswers) {
        foreach ($submittedAnswers as $questionId => $answer) {
            echo "Question ID {$questionId}: Answer = '{$answer}'\n";
        }
    }
    echo "\n";

    // Manually recalculate the score
    echo "MANUAL SCORE CALCULATION:\n";
    echo "========================\n";
    
    $correctCount = 0;
    $totalCount = count($questions);
    
    foreach ($questions as $index => $question) {
        $questionId = (string)$question->id;
        $studentAnswer = $submittedAnswers[$questionId] ?? null;
        $correctAnswer = $question->correct_answer;
        
        echo "Question " . ($index + 1) . " (ID: {$questionId}):\n";
        echo "  Student Answer: '" . ($studentAnswer ?? 'NULL') . "'\n";
        echo "  Correct Answer: '{$correctAnswer}'\n";
        
        $isCorrect = false;
        
        if ($studentAnswer !== null) {
            if ($question->question_type === 'multiple_choice') {
                // Check if student answer is a letter (A, B, C, etc.)
                if (is_string($studentAnswer) && preg_match('/^[A-Z]$/', $studentAnswer)) {
                    // Convert letter (A, B, C) to index (0, 1, 2)
                    $convertedAnswer = (string)(ord($studentAnswer) - 65);
                    echo "  Converted Answer: '{$convertedAnswer}'\n";
                    $isCorrect = $convertedAnswer === (string)$correctAnswer;
                } else {
                    // Direct comparison
                    $isCorrect = (string)$studentAnswer === (string)$correctAnswer;
                }
            } else {
                // For true/false questions
                $isCorrect = $studentAnswer === $correctAnswer;
            }
        }
        
        echo "  Is Correct: " . ($isCorrect ? 'YES' : 'NO') . "\n";
        
        if ($isCorrect) {
            $correctCount++;
        }
        echo "\n";
    }
    
    $calculatedScore = $totalCount > 0 ? ($correctCount / $totalCount) * 100 : 0;
    
    echo "SCORE CALCULATION RESULTS:\n";
    echo "Correct Answers: {$correctCount}\n";
    echo "Total Questions: {$totalCount}\n";
    echo "Calculated Score: " . number_format($calculatedScore, 2) . "%\n";
    echo "Database Score: " . number_format($latestAttempt->score, 2) . "%\n\n";

    // Check if there's a mismatch
    if (abs($calculatedScore - $latestAttempt->score) > 0.01) {
        echo "🚨 SCORE MISMATCH DETECTED!\n";
        echo "The manually calculated score ({$calculatedScore}%) does not match the database score ({$latestAttempt->score}%)\n\n";
        
        // Let's check the submission logic more closely
        echo "DEBUGGING SUBMISSION LOGIC:\n";
        echo "==========================\n";
        
        // Simulate the exact logic from the controller
        foreach ($questions as $question) {
            $questionId = (string)$question->id;
            $studentAnswer = $submittedAnswers[$questionId] ?? null;
            $correctAnswer = $question->correct_answer;
            
            echo "Processing Question ID {$questionId}:\n";
            echo "  Raw student answer: '" . json_encode($studentAnswer) . "'\n";
            echo "  Raw correct answer: '" . json_encode($correctAnswer) . "'\n";
            echo "  Student answer type: " . gettype($studentAnswer) . "\n";
            echo "  Correct answer type: " . gettype($correctAnswer) . "\n";
            
            if ($studentAnswer !== null) {
                $isCorrect = false;
                
                if ($question->question_type === 'multiple_choice') {
                    // Handle letter to index conversion if needed
                    if (is_string($studentAnswer) && preg_match('/^[A-Z]$/', $studentAnswer)) {
                        // Convert letter (A, B, C) to index (0, 1, 2)
                        $convertedAnswer = (string)(ord($studentAnswer) - 65);
                        echo "  Converted to index: '{$convertedAnswer}'\n";
                        $isCorrect = $convertedAnswer === (string)$correctAnswer;
                        echo "  Comparison: '{$convertedAnswer}' === '" . (string)$correctAnswer . "' = " . ($isCorrect ? 'TRUE' : 'FALSE') . "\n";
                    } else {
                        // Direct comparison (both should be strings)
                        $isCorrect = (string)$studentAnswer === (string)$correctAnswer;
                        echo "  Direct comparison: '" . (string)$studentAnswer . "' === '" . (string)$correctAnswer . "' = " . ($isCorrect ? 'TRUE' : 'FALSE') . "\n";
                    }
                } else {
                    // For other question types (true/false, etc.)
                    $isCorrect = $studentAnswer === $correctAnswer;
                    echo "  True/False comparison: '" . $studentAnswer . "' === '" . $correctAnswer . "' = " . ($isCorrect ? 'TRUE' : 'FALSE') . "\n";
                }
            }
            echo "\n";
        }
    } else {
        echo "✅ Scores match! The calculation logic appears correct.\n";
    }

    // Check if we need to update the database
    if ($latestAttempt->score == 0 && $calculatedScore > 0) {
        echo "FIXING DATABASE SCORE:\n";
        echo "=====================\n";
        
        $updateResult = DB::table('quiz_attempts')
            ->where('attempt_id', $latestAttempt->attempt_id)
            ->update([
                'score' => $calculatedScore,
                'correct_answers' => $correctCount,
                'updated_at' => now()
            ]);
        
        if ($updateResult) {
            echo "✅ Database updated successfully!\n";
            echo "New score: " . number_format($calculatedScore, 2) . "%\n";
            echo "Correct answers: {$correctCount}\n";
        } else {
            echo "❌ Failed to update database\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
