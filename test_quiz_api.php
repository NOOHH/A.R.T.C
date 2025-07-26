<?php

// Simple test script to verify QuizAPI integration
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== QuizAPI Integration Test ===\n";

try {
    echo "1. Creating QuizAPI service...\n";
    $service = new App\Services\QuizApiService();
    echo "   ✓ Service created successfully!\n";

    echo "2. Testing connection...\n";
    $connected = $service->testConnection();
    echo "   " . ($connected ? "✓ Connection: SUCCESS" : "✗ Connection: FAILED") . "\n";

    if ($connected) {
        echo "3. Getting available categories...\n";
        $categories = $service->getCategories();
        echo "   Available categories: " . count($categories) . "\n";
        foreach ($categories as $key => $name) {
            echo "   - $key: $name\n";
        }

        echo "4. Testing question retrieval (Linux, 2 questions)...\n";
        $questions = $service->getQuestions(['limit' => 2, 'category' => 'linux']);
        echo "   Retrieved: " . count($questions) . " questions\n";

        foreach ($questions as $i => $question) {
            echo "\n   Question " . ($i + 1) . ":\n";
            echo "   Q: " . $question['question'] . "\n";
            echo "   Options:\n";
            foreach ($question['options'] as $key => $option) {
                $marker = ($key === $question['correct_answer']) ? ' [CORRECT]' : '';
                echo "     $key) $option$marker\n";
            }
            echo "   Difficulty: " . $question['difficulty'] . "\n";
            echo "   Category: " . $question['category'] . "\n";
        }

        echo "\n5. Testing shouldUseQuizApi function...\n";
        $testTopics = ['Linux Quiz', 'JavaScript Programming', 'Docker Containers', 'Custom Document Topic'];
        foreach ($testTopics as $topic) {
            $shouldUse = $service->shouldUseQuizApi($topic);
            echo "   '$topic': " . ($shouldUse ? "Use QuizAPI" : "Use document processing") . "\n";
        }
    }

    echo "\n=== Test Completed Successfully ===\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
