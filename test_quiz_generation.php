<?php

// Simple test to check quiz generation
echo "Testing quiz generation...\n";

// Include Laravel bootstrap
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Professor;
use App\Models\Quiz;
use App\Models\QuizQuestion;

try {
    // Test professor authentication
    $professor = Professor::find(8); // Using professor ID from logs
    if (!$professor) {
        echo "ERROR: Professor with ID 8 not found\n";
        exit;
    }
    echo "✓ Professor found: " . $professor->professor_first_name . " " . $professor->professor_last_name . "\n";
    
    // Test program assignment
    $programId = 35; // From the error logs
    $programAssignment = $professor->programs()->where('programs.program_id', $programId)->exists();
    echo $programAssignment ? "✓ Professor is assigned to program $programId\n" : "✗ Professor NOT assigned to program $programId\n";
    
    // Test quiz creation
    $testQuiz = Quiz::create([
        'professor_id' => $professor->professor_id,
        'program_id' => $programId,
        'module_id' => 56,
        'course_id' => 30,
        'content_id' => 27,
        'quiz_title' => 'Test Quiz Generation',
        'instructions' => 'This is a test quiz',
        'randomize_order' => false,
        'tags' => ['test'],
        'is_draft' => false,
        'total_questions' => 1,
        'time_limit' => 60,
        'document_path' => 'test-path',
        'is_active' => true,
        'created_at' => now(),
    ]);
    
    echo "✓ Quiz created successfully with ID: " . $testQuiz->quiz_id . "\n";
    
    // Test question creation
    $testQuestion = QuizQuestion::create([
        'quiz_id' => $testQuiz->quiz_id,
        'quiz_title' => $testQuiz->quiz_title,
        'program_id' => $testQuiz->program_id,
        'question_text' => 'What is 2 + 2?',
        'question_type' => 'multiple_choice',
        'options' => [
            'A' => '3',
            'B' => '4',
            'C' => '5',
            'D' => '6'
        ],
        'correct_answer' => 'B',
        'points' => 1,
        'is_active' => true,
        'created_by_professor' => $professor->professor_id,
    ]);
    
    echo "✓ Question created successfully with ID: " . $testQuestion->id . "\n";
    
    // Clean up
    $testQuestion->delete();
    $testQuiz->delete();
    echo "✓ Test data cleaned up\n";
    
    echo "\n=== ALL TESTS PASSED ===\n";
    echo "The quiz generation should work. Check the web interface.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
