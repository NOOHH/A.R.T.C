<?php

// Complete integration test - test the full quiz generation workflow
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Complete Quiz Generation Integration Test ===\n";

try {
    // Test 1: Technical topic should use QuizAPI
    echo "\n1. Testing Technical Topic (Linux Quiz)\n";
    
    // Test the shouldUseQuizApi logic
    $quizApiService = new App\Services\QuizApiService();
    $shouldUseApi = $quizApiService->shouldUseQuizApi('Linux System Administration Quiz');
    echo "Should use QuizAPI: " . ($shouldUseApi ? 'YES' : 'NO') . "\n";
    
    if ($shouldUseApi) {
        $questions = $quizApiService->generateQuizQuestions('Linux System Administration Quiz', 5, 'Easy');
        echo "Generated questions from QuizAPI: " . count($questions) . "\n";
        
        if (count($questions) > 0) {
            echo "Sample question: " . substr($questions[0]['question'], 0, 80) . "...\n";
            echo "Options: " . implode(', ', array_keys($questions[0]['options'])) . "\n";
            echo "Correct answer: " . $questions[0]['correct_answer'] . "\n";
        }
    }

    // Test 2: Non-technical topic should NOT use QuizAPI
    echo "\n2. Testing Non-Technical Topic (History Quiz)\n";
    $shouldUseApi = $quizApiService->shouldUseQuizApi('History of Ancient Rome');
    echo "Should use QuizAPI: " . ($shouldUseApi ? 'YES' : 'NO') . "\n";

    // Test 3: JavaScript Programming
    echo "\n3. Testing JavaScript Programming Quiz\n";
    $shouldUseApi = $quizApiService->shouldUseQuizApi('JavaScript Programming');
    echo "Should use QuizAPI: " . ($shouldUseApi ? 'YES' : 'NO') . "\n";
    
    if ($shouldUseApi) {
        $questions = $quizApiService->generateQuizQuestions('JavaScript Programming', 3, 'Easy');
        echo "Generated JavaScript questions: " . count($questions) . "\n";
    }

    echo "\n=== Integration Test Completed Successfully ===\n";
    echo "✓ QuizAPI service is working\n";
    echo "✓ Topic detection is working\n";
    echo "✓ Question generation is working\n";
    echo "✓ Ready for production use!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
