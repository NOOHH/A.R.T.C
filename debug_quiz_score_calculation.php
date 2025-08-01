<?php
/**
 * Debug script to test quiz answer format and score calculation issue
 */

// Bootstrap the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Find all questions with 'EEE' as the question text
$eeeQuestions = \App\Models\QuizQuestion::where('question_text', 'EEE')->get();
echo "Found " . $eeeQuestions->count() . " questions with text 'EEE'\n";
foreach ($eeeQuestions as $question) {
    echo "ID: " . $question->question_id . ", Quiz ID: " . $question->quiz_id . "\n";
    echo "Options: " . json_encode($question->options) . "\n";
    echo "Correct answer: " . $question->correct_answer . "\n";
}
echo "\n";

// Get the attempt and check its stored answers
$attemptId = 13; // From the URL in your error message
$attempt = \App\Models\QuizAttempt::find($attemptId);
echo "Attempt #$attemptId stored answers: " . json_encode($attempt->answers) . "\n\n";

// Create a simulated answer for the question (pretending user selected 'A')
$studentAnswer = 'A';
$convertedAnswer = '0'; // After letter-to-index conversion
$answers = [];

if (!empty($eeeQuestions) && $eeeQuestions->first()->question_id !== null && $eeeQuestions->first()->question_id !== '') {
    $answers[$eeeQuestions->first()->question_id] = $convertedAnswer;
} else {
    // If the question ID is empty/null, use the key from the attempt's answers
    if ($attempt && $attempt->answers) {
        $keys = array_keys((array)$attempt->answers);
        if (!empty($keys)) {
            $answers[$keys[0]] = $convertedAnswer;
            echo "Using key from attempt answers: " . $keys[0] . "\n";
        }
    }
}

echo "=== Quiz Answer Validation ===\n\n";

// First, check if the quiz attempt exists
$attempt = \App\Models\QuizAttempt::find($attemptId);
if (!$attempt) {
    echo "Quiz attempt #$attemptId not found!\n";
    exit;
}

// Get the quiz details
$quiz = \App\Models\Quiz::find($attempt->quiz_id);
if (!$quiz) {
    echo "Quiz not found for attempt #$attemptId!\n";
    exit;
}

echo "Attempt ID: " . $attemptId . "\n";
echo "Quiz ID: " . $quiz->quiz_id . "\n";
echo "Quiz Title: " . $quiz->quiz_title . "\n";
echo "Student ID: " . $attempt->student_id . "\n\n";

// Get all questions for this quiz
$questions = \App\Models\QuizQuestion::where('quiz_id', $quiz->quiz_id)->get();
echo "Quiz has " . $questions->count() . " questions.\n\n";

// Process the answers
$correctCount = 0;
$totalQuestions = $questions->count();

echo "=== Processing Answers ===\n\n";

// Get the original student answers from the attempt
$storedAnswers = $attempt->answers;

// Manually check each answer
foreach ($questions as $question) {
    $questionId = $question->question_id ?: '(empty)';
    $correctAnswer = $question->correct_answer;
    
    // Check if we have a stored answer for this question
    $studentAnswerFound = false;
    $studentAnswer = null;
    
    // Special case: Since we have a question with an empty ID but we know the key from stored answers
    if (empty($question->question_id) && $attempt->answers) {
        // Use the first key from the stored answers
        $keys = array_keys((array)$attempt->answers);
        if (!empty($keys)) {
            $fakeQuestionId = $keys[0]; // '416' in our case
            $studentAnswer = $storedAnswers[$fakeQuestionId] ?? null;
            $studentAnswerFound = true;
            echo "Using answer for key $fakeQuestionId since question ID is empty\n";
        }
    } else {
        // Normal case - use the question ID
        $studentAnswer = $answers[$questionId] ?? null;
    }
    
    echo "Question ID: " . $questionId . "\n";
    echo "Question Text: " . $question->question_text . "\n";
    echo "Correct Answer: " . $correctAnswer . " (type: " . gettype($correctAnswer) . ")\n";
    echo "Student Answer: " . ($studentAnswer ?? 'Not answered') . " (type: " . gettype($studentAnswer) . ")\n";
    
    // Compare the answers (handle different formats)
    $isCorrect = false;
    if ($studentAnswer !== null) {
        if ($question->question_type === 'multiple_choice') {
            // Original student answer is letter 'A'
            // We need to convert it to index '0' for comparison
            if ($studentAnswer === 'A') {
                $convertedAnswer = '0';
                echo "Converting answer from 'A' to '0' for comparison\n";
                $isCorrect = $convertedAnswer === $correctAnswer;
            } else {
                // Handle both string and numeric comparisons
                $isCorrect = (string)$studentAnswer === (string)$correctAnswer;
            }
        } elseif ($question->question_type === 'true_false') {
            $isCorrect = strtolower($studentAnswer) === strtolower($correctAnswer);
        } else {
            // For other question types
            $isCorrect = $studentAnswer === $correctAnswer;
        }
    }
    
    echo "Is Correct: " . ($isCorrect ? "YES" : "NO") . "\n\n";
    
    if ($isCorrect) {
        $correctCount++;
    }
}

// Calculate the score
$score = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
echo "=== Score Calculation ===\n";
echo "Correct Answers: " . $correctCount . "\n";
echo "Total Questions: " . $totalQuestions . "\n";
echo "Calculated Score: " . number_format($score, 1) . "%\n\n";

// Let's manually update the attempt to test our fix
echo "=== Manual Update Test ===\n";
echo "Current stored score in DB: " . $attempt->score . "\n";
echo "Current correct_answers in DB: " . $attempt->correct_answers . "\n";

// Simulate updating the attempt with correct data
$attempt->correct_answers = $correctCount;
$attempt->score = $score;
echo "New values that should be stored - score: $score, correct_answers: $correctCount\n\n";

// Instead of saving, let's just show what we would update
echo "=== Database Update Simulation ===\n";
echo "UPDATE quiz_attempts SET correct_answers = " . $correctCount . 
     ", score = " . $score . 
     " WHERE attempt_id = " . $attemptId . ";\n\n";

// Also, let's check if there might be a type mismatch issue
echo "=== Type Analysis ===\n";
echo "Correct Answer Type: " . gettype($correctAnswer) . "\n";
echo "Student Answer Type: " . gettype($studentAnswer) . "\n";
echo "Raw Student Answer '0' == Correct Answer '$correctAnswer': " . (($studentAnswer == $correctAnswer) ? 'true' : 'false') . "\n";
echo "Raw Student Answer '0' === Correct Answer '$correctAnswer': " . (($studentAnswer === $correctAnswer) ? 'true' : 'false') . "\n";
