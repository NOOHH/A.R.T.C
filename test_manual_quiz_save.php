<?php
/**
 * Manual Quiz Save Test
 * This script tests the manual quiz save functionality
 */

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Professor\QuizGeneratorController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

echo "=== Manual Quiz Save Test ===\n";

// Set professor session (simulating logged in professor)
Session::put('professor_id', 8);
Session::put('logged_in', true);
Session::put('user_role', 'professor');
Session::put('user_type', 'professor');
Session::put('user_id', 8);

echo "✓ Professor session set (ID: 8)\n";

// Create test data matching frontend format
$testData = [
    'title' => 'TEST QUIZ - PHP Script',
    'description' => 'This is a test quiz created via PHP script',
    'program_id' => '41',
    'module_id' => '79', 
    'course_id' => '53',
    'professor_id' => 8,
    'time_limit' => 60,
    'max_attempts' => 1,
    'is_draft' => true,
    'questions' => [
        [
            'question_text' => 'What is PHP?',
            'question_type' => 'multiple_choice',
            'options' => ['A scripting language', 'A database', 'A framework', 'An operating system'],
            'correct_answers' => ['A scripting language'],
            'explanation' => 'PHP is a server-side scripting language',
            'points' => 1,
            'order' => 1
        ],
        [
            'question_text' => 'Laravel is a PHP framework?',
            'question_type' => 'true_false', 
            'options' => ['True', 'False'],
            'correct_answers' => ['True'],
            'explanation' => 'Laravel is indeed a PHP web framework',
            'points' => 1,
            'order' => 2
        ]
    ],
    '_token' => 'test_token'
];

echo "✓ Test data prepared\n";
echo "  - Title: " . $testData['title'] . "\n";
echo "  - Questions: " . count($testData['questions']) . "\n";
echo "  - Program ID: " . $testData['program_id'] . "\n";
echo "  - Module ID: " . $testData['module_id'] . "\n";
echo "  - Course ID: " . $testData['course_id'] . "\n";

// Create request with proper JSON setup
$request = Request::create(
    '/professor/quiz-generator/save-manual',
    'POST',
    [],
    [],
    [],
    [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
    ],
    json_encode($testData)
);

echo "✓ Request created\n";

try {
    // Create controller instance
    $controller = new QuizGeneratorController(app(\App\Services\GeminiQuizService::class));
    
    echo "✓ Controller instantiated\n";
    
    // Call the method
    echo "\n--- Calling saveManualQuiz method ---\n";
    $response = $controller->saveManualQuiz($request);
    
    echo "✓ Method called successfully\n";
    
    // Get response data
    $responseData = json_decode($response->getContent(), true);
    $statusCode = $response->getStatusCode();
    
    echo "\n=== RESPONSE ===\n";
    echo "Status Code: " . $statusCode . "\n";
    echo "Response Data:\n";
    print_r($responseData);
    
    if ($statusCode === 200 && $responseData['success']) {
        echo "\n✅ SUCCESS: Quiz saved successfully!\n";
        echo "Quiz ID: " . $responseData['quiz_id'] . "\n";
        echo "Status: " . $responseData['status'] . "\n";
        echo "Message: " . $responseData['message'] . "\n";
        
        // Verify in database
        $quiz = \App\Models\Quiz::find($responseData['quiz_id']);
        if ($quiz) {
            echo "✓ Quiz found in database\n";
            echo "  - Title: " . $quiz->quiz_title . "\n";
            echo "  - Status: " . $quiz->status . "\n";
            echo "  - Questions count: " . $quiz->questions()->count() . "\n";
            
            $questions = $quiz->questions()->get();
            foreach ($questions as $i => $question) {
                echo "  - Question " . ($i+1) . ": " . substr($question->question_text, 0, 50) . "...\n";
            }
        } else {
            echo "❌ Quiz not found in database!\n";
        }
    } else {
        echo "\n❌ FAILED: Quiz save failed\n";
        if (isset($responseData['errors'])) {
            echo "Validation Errors:\n";
            foreach ($responseData['errors'] as $field => $errors) {
                echo "  - $field: " . implode(', ', $errors) . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "\n❌ EXCEPTION occurred:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test completed ===\n";
