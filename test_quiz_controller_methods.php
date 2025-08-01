<?php
/**
 * Test Quiz Controller Methods
 * This tests if the newly added methods exist and are callable
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Professor\QuizGeneratorController;
use App\Services\GeminiQuizService;
use ReflectionClass;

echo "=== Quiz Controller Methods Test ===\n";

try {
    // Create controller instance
    $geminiService = app(GeminiQuizService::class);
    $controller = new QuizGeneratorController($geminiService);
    
    echo "✓ Controller instantiated successfully\n";
    
    // Get all public methods
    $reflection = new ReflectionClass($controller);
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    
    $expectedMethods = [
        'index', 'generateAIQuestions', 'saveQuizWithQuestions', 'saveManualQuiz', 'save',
        'preview', 'previewQuiz', 'publish', 'publishQuiz', 'archive', 'archiveQuiz',
        'delete', 'deleteQuiz', 'deleteQuestion', 'viewQuestions', 'editQuestions',
        'editQuiz', 'getQuizForEdit', 'updateQuestion', 'addQuestion', 
        'restoreQuiz', 'moveToDraft', 'getModulesByProgram', 'getCoursesByModule', 'getContentsByCourse'
    ];
    
    echo "\n--- Checking Required Methods ---\n";
    $foundMethods = [];
    
    foreach ($methods as $method) {
        if (!$method->isConstructor() && !$method->isDestructor()) {
            $foundMethods[] = $method->getName();
        }
    }
    
    foreach ($expectedMethods as $expectedMethod) {
        if (in_array($expectedMethod, $foundMethods)) {
            echo "✅ $expectedMethod - EXISTS\n";
        } else {
            echo "❌ $expectedMethod - MISSING\n";
        }
    }
    
    echo "\n--- Additional Methods Found ---\n";
    $additionalMethods = array_diff($foundMethods, $expectedMethods);
    foreach ($additionalMethods as $additionalMethod) {
        echo "ℹ️  $additionalMethod\n";
    }
    
    echo "\n--- Method Count Summary ---\n";
    echo "Expected methods: " . count($expectedMethods) . "\n";
    echo "Found methods: " . count($foundMethods) . "\n";
    echo "Missing methods: " . count(array_diff($expectedMethods, $foundMethods)) . "\n";
    echo "Additional methods: " . count($additionalMethods) . "\n";
    
    // Test if Quiz model can be loaded
    $quiz = \App\Models\Quiz::find(42);
    if ($quiz) {
        echo "\n✅ Quiz ID 42 exists in database\n";
        echo "  - Title: " . $quiz->quiz_title . "\n";
        echo "  - Status: " . $quiz->status . "\n";
        echo "  - Professor ID: " . $quiz->professor_id . "\n";
        echo "  - Questions count: " . $quiz->questions()->count() . "\n";
    } else {
        echo "\n❌ Quiz ID 42 not found in database\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Test failed: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Completed ===\n";
