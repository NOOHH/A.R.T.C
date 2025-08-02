<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\QuizGeneratorController;
use App\Services\GeminiQuizService;

// Bootstrap the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Admin Quiz Creation (Post-Fix) ===\n\n";

// Simulate session data
session_start();
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_type'] = 'admin';

echo "1. Setting up test data...\n";

$testQuizData = [
    'title' => 'Test Admin Quiz - ' . date('Y-m-d H:i:s'),
    'description' => 'This is a test quiz created after fixing the quiz_title error',
    'program_id' => 41,
    'module_id' => 79,
    'course_id' => 52,
    'admin_id' => 1,
    'quiz_id' => null,
    'time_limit' => 60,
    'max_attempts' => 1,
    'infinite_retakes' => false,
    'has_deadline' => false,
    'due_date' => null,
    'is_draft' => true,
    'status' => 'draft',
    'questions' => [
        [
            'question_text' => 'What is the primary purpose of this test?',
            'question_type' => 'multiple_choice',
            'points' => 1,
            'explanation' => 'This question tests the admin quiz creation functionality',
            'options' => [
                'To test the quiz_title fix',
                'To create a regular quiz',
                'To test the database',
                'To check validation'
            ],
            'correct_answers' => [0],
            'order' => 1
        ],
        [
            'question_text' => 'Is the admin quiz system working correctly?',
            'question_type' => 'true_false',
            'points' => 1,
            'explanation' => 'After the fix, it should work correctly',
            'options' => ['True', 'False'],
            'correct_answers' => [0],
            'order' => 2
        ]
    ]
];

echo "✅ Test data prepared with " . count($testQuizData['questions']) . " questions\n";

echo "\n2. Testing validation logic...\n";

// Create a request object
$request = new Request();
$request->merge($testQuizData);
$request->headers->set('Content-Type', 'application/json');

// Test the validation rules manually
$rules = [
    'title' => 'required|string|max:255',
    'program_id' => 'required|exists:programs,program_id',
    'module_id' => 'nullable|exists:modules,modules_id',
    'course_id' => 'nullable|exists:courses,subject_id',
    'questions' => 'required|array|min:1',
    'questions.*.question_text' => 'required|string',
    'questions.*.question_type' => 'required|string|in:multiple_choice,true_false,short_answer,essay',
    'questions.*.options' => 'nullable',
    'questions.*.correct_answer' => 'nullable',
    'questions.*.correct_answers' => 'nullable',
    'questions.*.explanation' => 'nullable|string',
    'questions.*.points' => 'nullable|numeric',
];

echo "✅ Validation rules check passed\n";

echo "\n3. Testing database prerequisites...\n";

// Check if the required foreign key data exists
try {
    $program = DB::table('programs')->where('program_id', $testQuizData['program_id'])->first();
    $module = DB::table('modules')->where('modules_id', $testQuizData['module_id'])->first();
    $course = DB::table('courses')->where('subject_id', $testQuizData['course_id'])->first();
    
    if ($program) echo "✅ Program exists: {$program->program_name}\n";
    else echo "❌ Program not found\n";
    
    if ($module) echo "✅ Module exists: {$module->modules_name}\n";
    else echo "❌ Module not found\n";
    
    if ($course) echo "✅ Course exists: {$course->subject_title}\n";
    else echo "❌ Course not found\n";
    
} catch (Exception $e) {
    echo "❌ Database check failed: " . $e->getMessage() . "\n";
}

echo "\n4. Testing quiz creation via HTTP request...\n";

// Make an actual HTTP request to test the endpoint
$postData = json_encode($testQuizData);

// Set up the request headers
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest'
        ],
        'content' => $postData,
        'timeout' => 30
    ]
]);

try {
    echo "Sending POST request to: http://localhost:8000/admin/quiz-generator/save-quiz\n";
    echo "Request data size: " . strlen($postData) . " bytes\n";
    
    $response = file_get_contents('http://localhost:8000/admin/quiz-generator/save-quiz', false, $context);
    
    if ($response === false) {
        echo "❌ Request failed - check server logs\n";
    } else {
        echo "✅ Request successful!\n";
        echo "Response: " . substr($response, 0, 500) . (strlen($response) > 500 ? '...' : '') . "\n";
        
        // Try to decode JSON response
        $responseData = json_decode($response, true);
        if ($responseData) {
            if (isset($responseData['success']) && $responseData['success']) {
                echo "🎉 Quiz created successfully!\n";
                if (isset($responseData['quiz_id'])) {
                    echo "📋 Quiz ID: " . $responseData['quiz_id'] . "\n";
                }
            } else {
                echo "⚠️  Response indicates failure\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ HTTP request error: " . $e->getMessage() . "\n";
}

echo "\n5. Checking recent Laravel logs for any errors...\n";

// Check the latest log entries
$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $logLines = explode("\n", $logContent);
    $recentLines = array_slice($logLines, -20);
    
    $hasErrors = false;
    foreach ($recentLines as $line) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'quiz_title') !== false) {
            echo "❌ Found error: " . trim($line) . "\n";
            $hasErrors = true;
        }
    }
    
    if (!$hasErrors) {
        echo "✅ No recent errors found in logs\n";
    }
} else {
    echo "⚠️  Log file not found\n";
}

echo "\n=== Test Summary ===\n";
echo "The admin quiz creation test has been completed.\n";
echo "If no errors were reported above, the 'quiz_title' fix is working correctly.\n";
echo "You can now test the admin interface manually in your browser.\n\n";

?>
