<?php
require_once 'vendor/autoload.php';

// Initialize Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a fake request to simulate admin quiz creation
$request = Illuminate\Http\Request::create('/admin/quiz-generator/save-quiz', 'POST', [
    'title' => 'Test Admin Quiz',
    'description' => 'Test Description',
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
            'question_text' => 'Test Question',
            'question_type' => 'multiple_choice',
            'points' => 1,
            'options' => ['A', 'B', 'C', 'D'],
            'correct_answers' => [0],
            'order' => 1
        ]
    ]
]);

// Set JSON content type and body
$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');

// Start session and set admin user
session_start();
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_type'] = 'admin';

echo "=== Testing Admin Quiz Creation Fix ===\n";
echo "Request data:\n";
print_r($request->all());

try {
    // Instantiate the controller directly to test the method
    $controller = new \App\Http\Controllers\Admin\QuizGeneratorController();
    
    echo "\n=== Testing saveQuizWithQuestions method directly ===\n";
    
    // Call the method directly
    $response = $controller->saveQuizWithQuestions($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test completed ===\n";
