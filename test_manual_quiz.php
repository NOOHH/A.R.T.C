<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing manual quiz creation...\n";

// Now test quiz creation manually
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Professor;

try {
    $quiz = Quiz::create([
        'professor_id' => 8,
        'program_id' => 35,
        'module_id' => 55,
        'course_id' => 29,
        'content_id' => 25,
        'quiz_title' => 'Manual Test Quiz',
        'instructions' => 'Test instructions',
        'randomize_order' => false,
        'tags' => ['test', 'manual'],
        'is_draft' => false,
        'total_questions' => 1,
        'time_limit' => 60,
        'document_path' => 'test.pdf',
        'is_active' => true,
    ]);
    
    echo "Quiz created with ID: " . $quiz->quiz_id . "\n";
    
    $question = QuizQuestion::create([
        'quiz_id' => $quiz->quiz_id,
        'quiz_title' => $quiz->quiz_title,
        'program_id' => $quiz->program_id,
        'question_text' => 'What is the capital of France?',
        'question_type' => 'multiple_choice',
        'options' => ['A' => 'London', 'B' => 'Berlin', 'C' => 'Paris', 'D' => 'Madrid'],
        'correct_answer' => 'C',
        'points' => 1,
        'is_active' => true,
        'created_by_professor' => 8,
    ]);
    
    echo "Question created with ID: " . $question->id . "\n";
    echo "Manual quiz creation successful!\n";
    
} catch (Exception $e) {
    echo "Error creating quiz: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nDone!\n";
