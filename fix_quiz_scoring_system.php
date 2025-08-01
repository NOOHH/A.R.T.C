<?php
/**
 * Fix the quiz scoring system by addressing the question ID mismatch
 */

// Bootstrap the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIXING QUIZ SCORING SYSTEM ===\n\n";

// The quiz_questions table uses 'id' as primary key, not 'question_id'
// Let's fix the scoring logic to use the correct key mapping

// Function to properly score quiz attempts
function scoreQuizAttempt($attemptId) {
    $attempt = \App\Models\QuizAttempt::find($attemptId);
    if (!$attempt) {
        return "Attempt not found";
    }
    
    // Get all questions for this quiz using the correct table structure
    $questions = \Illuminate\Support\Facades\DB::table('quiz_questions')
        ->where('quiz_id', $attempt->quiz_id)
        ->get();
    
    $answers = $attempt->answers;
    $correctCount = 0;
    $totalQuestions = count($questions);
    
    echo "Scoring attempt {$attemptId}:\n";
    echo "Stored answers: " . json_encode($answers) . "\n";
    
    foreach ($questions as $question) {
        // Use the primary key 'id' as the question identifier
        $questionKey = (string)$question->id; // Convert to string for consistency
        $studentAnswer = $answers[$questionKey] ?? null;
        $correctAnswer = $question->correct_answer;
        
        echo "Question {$questionKey}: Student answer = '$studentAnswer', Correct = '$correctAnswer'\n";
        
        // Compare answers
        $isCorrect = false;
        if ($studentAnswer !== null) {
            if ($question->question_type === 'multiple_choice') {
                // Handle letter to index conversion if needed
                if (preg_match('/^[A-Z]$/', $studentAnswer)) {
                    $convertedAnswer = (string)(ord($studentAnswer) - 65);
                    $isCorrect = $convertedAnswer === $correctAnswer;
                    echo "Converted '$studentAnswer' to '$convertedAnswer' for comparison\n";
                } else {
                    // Direct comparison (both should be strings)
                    $isCorrect = (string)$studentAnswer === (string)$correctAnswer;
                }
            } else {
                $isCorrect = $studentAnswer === $correctAnswer;
            }
        }
        
        echo "Is correct: " . ($isCorrect ? "YES" : "NO") . "\n";
        
        if ($isCorrect) {
            $correctCount++;
        }
    }
    
    $score = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
    
    // Update the attempt
    \Illuminate\Support\Facades\DB::table('quiz_attempts')
        ->where('attempt_id', $attemptId)
        ->update([
            'score' => $score,
            'correct_answers' => $correctCount,
            'updated_at' => now()
        ]);
    
    echo "Updated score: {$score}%, Correct answers: {$correctCount}/{$totalQuestions}\n\n";
    return "Score updated to {$score}%";
}

// Fix all the recent attempts for quiz 47
echo "1. FIXING ALL QUIZ ATTEMPTS:\n";
$attempts = \App\Models\QuizAttempt::where('quiz_id', 47)
    ->where('status', 'completed')
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($attempts as $attempt) {
    echo "Fixing attempt {$attempt->attempt_id}:\n";
    $result = scoreQuizAttempt($attempt->attempt_id);
    echo "Result: $result\n";
    echo "---\n";
}

// Verify the fixes
echo "\n2. VERIFICATION - CHECKING ALL UPDATED SCORES:\n";
$updatedAttempts = \App\Models\QuizAttempt::where('quiz_id', 47)
    ->where('status', 'completed')
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($updatedAttempts as $attempt) {
    echo "Attempt {$attempt->attempt_id}: Score = {$attempt->score}%, Answers = " . json_encode($attempt->answers) . "\n";
}

echo "\n=== FIX COMPLETED ===\n";
