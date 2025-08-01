<?php
/**
 * Debug script to test quiz answer submission and format matching
 */

// Bootstrap the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Quiz Answer Format Verification ===\n\n";

// Get the specific question for quiz ID 47
$question = \App\Models\QuizQuestion::where('quiz_id', 47)->first();

if (!$question) {
    echo "Question not found for quiz ID 47!\n";
    exit;
}

echo "Question ID: " . $question->question_id . "\n";
echo "Question Text: " . $question->question_text . "\n";
echo "Question Type: " . $question->question_type . "\n";

// Options handling
$options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
echo "\nOptions:\n";
foreach ($options as $index => $option) {
    echo "Index $index: $option" . (($index === 0 || $index === '0') ? " (First option)" : "") . "\n";
}

echo "\nCorrect Answer (stored value): " . $question->correct_answer . "\n";

// Test different answer formats
$letterMap = ['A', 'B', 'C', 'D', 'E', 'F'];
echo "\nTesting answer conversions:\n";

for ($i = 0; $i < count($options); $i++) {
    $letter = $letterMap[$i] ?? $i;
    $isCorrect = ($i == $question->correct_answer) ? "YES (CORRECT)" : "NO";
    $isCorrectLetter = ($letter == $question->correct_answer) ? "YES (CORRECT)" : "NO";
    
    echo "Index $i / Letter $letter - Matches index-based correct answer? $isCorrect\n";
    echo "Index $i / Letter $letter - Matches letter-based correct answer? $isCorrectLetter\n";
}

// Checking existing attempt
$attempt = \App\Models\QuizAttempt::find(13);
if ($attempt) {
    echo "\nExisting attempt answers: " . print_r($attempt->answers, true) . "\n";
    
    foreach ($attempt->answers as $qid => $answer) {
        echo "Question ID: $qid, Answer: $answer\n";
        
        if ($answer === 'A') {
            echo "User selected option A, which corresponds to index 0\n";
            echo "Correct answer is index " . $question->correct_answer . "\n";
            echo "Should be marked correct? " . ((0 == $question->correct_answer) ? "YES" : "NO") . "\n";
        }
    }
}
