<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing QuizAttempt creation...\n";

try {
    // Test data similar to what the controller would use
    $testData = [
        'quiz_id' => 42,
        'student_id' => '2025-07-00001',
        'started_at' => now(),
        'status' => 'in_progress',
        'answers' => [],
        'total_questions' => 14
    ];
    
    echo "Attempting to create QuizAttempt with data:\n";
    print_r($testData);
    
    $attempt = \App\Models\QuizAttempt::create($testData);
    
    echo "SUCCESS! QuizAttempt created with ID: " . $attempt->attempt_id . "\n";
    echo "Quiz ID: " . $attempt->quiz_id . "\n";
    echo "Student ID: " . $attempt->student_id . "\n";
    echo "Status: " . $attempt->status . "\n";
    
    // Clean up the test record
    $attempt->delete();
    echo "Test record cleaned up.\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo 'Trace: ' . $e->getTraceAsString() . "\n";
}
