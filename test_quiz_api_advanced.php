<?php

// Advanced test script to check QuizAPI question retrieval
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Advanced QuizAPI Test ===\n";

try {
    $service = new App\Services\QuizApiService();
    
    // Test different parameter combinations
    $testCases = [
        ['limit' => 2],
        ['limit' => 2, 'difficulty' => 'Easy'],
        ['limit' => 2, 'difficulty' => 'Medium'],
        ['limit' => 2, 'category' => 'linux'],
        ['limit' => 2, 'category' => 'uncategorized'],
        ['limit' => 2, 'category' => 'code'],
    ];

    foreach ($testCases as $i => $params) {
        echo "\nTest case " . ($i + 1) . ": " . json_encode($params) . "\n";
        $questions = $service->getQuestions($params);
        echo "Retrieved: " . count($questions) . " questions\n";
        
        if (count($questions) > 0) {
            $firstQuestion = $questions[0];
            echo "Sample question: " . substr($firstQuestion['question'], 0, 80) . "...\n";
            echo "Options count: " . count($firstQuestion['options']) . "\n";
            break; // Stop after first successful retrieval
        }
    }

    // Test the full generateQuizQuestions method
    echo "\n=== Testing generateQuizQuestions method ===\n";
    $topics = ['Linux', 'Programming', 'JavaScript', 'Docker'];
    
    foreach ($topics as $topic) {
        echo "\nTesting topic: '$topic'\n";
        $questions = $service->generateQuizQuestions($topic, 3, 'Easy');
        echo "Generated: " . count($questions) . " questions\n";
        
        if (count($questions) > 0) {
            echo "Sample: " . substr($questions[0]['question'], 0, 80) . "...\n";
        }
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
