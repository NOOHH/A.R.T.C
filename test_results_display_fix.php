<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING QUIZ RESULTS DISPLAY FIX ===\n\n";

// Test attempt 21 which had the issue
$attemptId = 21;
$attempt = \App\Models\QuizAttempt::with(['quiz.questions'])->find($attemptId);

if (!$attempt) {
    echo "Attempt $attemptId not found\n";
    exit;
}

echo "Testing Attempt $attemptId:\n";
echo "Quiz: " . $attempt->quiz->quiz_title . "\n";
echo "Student Answer Stored: " . json_encode($attempt->answers) . "\n";
echo "Score: " . $attempt->score . "%\n\n";

$quiz = $attempt->quiz;
$questions = $quiz->questions;
$studentAnswers = $attempt->answers;

echo "Processing results with new logic:\n";
echo "==================================\n";

foreach ($questions as $question) {
    $questionId = $question->id;
    $studentAnswer = $studentAnswers[$questionId] ?? null;
    
    echo "Question ID: $questionId\n";
    echo "Question: " . $question->question_text . "\n";
    echo "Stored Student Answer: $studentAnswer\n";
    echo "Stored Correct Answer: " . $question->correct_answer . "\n";
    
    // Convert student answer to letter format for display if it's numeric
    $studentAnswerDisplay = $studentAnswer;
    $correctAnswerDisplay = $question->correct_answer;
    
    if ($question->question_type === 'multiple_choice') {
        // If student answer is numeric (0, 1, 2), convert to letter (A, B, C)
        if (is_numeric($studentAnswer)) {
            $studentAnswerDisplay = chr(65 + (int)$studentAnswer);
            echo "Student Answer Display: $studentAnswerDisplay (converted from $studentAnswer)\n";
        }
        
        // If correct answer is numeric (0, 1, 2), convert to letter (A, B, C)
        if (is_numeric($correctAnswerDisplay)) {
            $correctAnswerDisplay = chr(65 + (int)$correctAnswerDisplay);
            echo "Correct Answer Display: $correctAnswerDisplay (converted from " . $question->correct_answer . ")\n";
        }
        
        // For comparison, normalize both to the same format
        $normalizedStudentAnswer = is_numeric($studentAnswer) ? (string)$studentAnswer : (string)(ord($studentAnswer) - 65);
        $normalizedCorrectAnswer = is_numeric($question->correct_answer) ? (string)$question->correct_answer : (string)(ord($question->correct_answer) - 65);
        
        echo "Normalized Student Answer: $normalizedStudentAnswer\n";
        echo "Normalized Correct Answer: $normalizedCorrectAnswer\n";
        
        $isCorrect = $normalizedStudentAnswer === $normalizedCorrectAnswer;
        echo "Is Correct: " . ($isCorrect ? "YES" : "NO") . "\n";
    } else {
        $isCorrect = $studentAnswer === $question->correct_answer;
        echo "Is Correct: " . ($isCorrect ? "YES" : "NO") . "\n";
    }
    
    echo "\nResult for display:\n";
    echo "- Student selected: $studentAnswerDisplay\n";
    echo "- Correct answer: $correctAnswerDisplay\n";
    echo "- Status: " . ($isCorrect ? "CORRECT ✓" : "INCORRECT ✗") . "\n";
    echo "\n" . str_repeat("-", 40) . "\n\n";
}

// Test the content lookup for back button
echo "Testing content lookup for back button:\n";
$content = \App\Models\ContentItem::where('content_type', 'quiz')
    ->whereRaw("JSON_EXTRACT(content_data, '$.quiz_id') = ?", [$quiz->quiz_id])
    ->first();

if ($content) {
    echo "Found content item ID: " . $content->id . "\n";
    echo "Content title: " . $content->title . "\n";
    echo "Back button should redirect to: /student/content/" . $content->id . "/view\n";
} else {
    echo "No content item found for this quiz\n";
    echo "Back button will redirect to dashboard\n";
}

echo "\n=== TEST COMPLETED ===\n";
