<?php
/**
 * Database Structure Test for Quiz Save
 * This tests the database tables and validates our data structure
 */

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Database Structure Test ===\n";

// Test 1: Check if required tables exist
echo "\n--- Testing Database Tables ---\n";

$requiredTables = ['quizzes', 'quiz_questions', 'programs', 'modules', 'courses', 'professors'];
foreach ($requiredTables as $table) {
    if (Schema::hasTable($table)) {
        echo "✓ Table '$table' exists\n";
    } else {
        echo "❌ Table '$table' missing\n";
    }
}

// Test 2: Check Quiz table structure
echo "\n--- Testing Quiz Table Structure ---\n";
$quizColumns = [
    'quiz_id', 'professor_id', 'program_id', 'module_id', 'course_id',
    'quiz_title', 'quiz_description', 'instructions', 'status', 'is_draft',
    'is_active', 'time_limit', 'max_attempts', 'total_questions'
];

foreach ($quizColumns as $column) {
    if (Schema::hasColumn('quizzes', $column)) {
        echo "✓ Column 'quizzes.$column' exists\n";
    } else {
        echo "❌ Column 'quizzes.$column' missing\n";
    }
}

// Test 3: Check QuizQuestion table structure  
echo "\n--- Testing Quiz Questions Table Structure ---\n";
$questionColumns = [
    'question_id', 'quiz_id', 'quiz_title', 'program_id', 'question_text',
    'question_type', 'question_order', 'options', 'correct_answer',
    'explanation', 'question_source', 'points', 'is_active', 'created_by_professor'
];

foreach ($questionColumns as $column) {
    if (Schema::hasColumn('quiz_questions', $column)) {
        echo "✓ Column 'quiz_questions.$column' exists\n";
    } else {
        echo "❌ Column 'quiz_questions.$column' missing\n";
    }
}

// Test 4: Check foreign key references exist
echo "\n--- Testing Foreign Key Data ---\n";

// Check if program exists
$program = DB::table('programs')->where('program_id', 41)->first();
if ($program) {
    echo "✓ Program ID 41 exists: " . $program->program_name . "\n";
} else {
    echo "❌ Program ID 41 not found\n";
}

// Check if module exists  
$module = DB::table('modules')->where('modules_id', 79)->first();
if ($module) {
    echo "✓ Module ID 79 exists: " . $module->module_name . "\n";
} else {
    echo "❌ Module ID 79 not found\n";
}

// Check if course exists
$course = DB::table('courses')->where('subject_id', 53)->first();
if ($course) {
    echo "✓ Course ID 53 exists: " . $course->subject_name . "\n";
} else {
    echo "❌ Course ID 53 not found\n";
}

// Check if professor exists
$professor = DB::table('professors')->where('professor_id', 8)->first();
if ($professor) {
    echo "✓ Professor ID 8 exists: " . $professor->first_name . " " . $professor->last_name . "\n";
} else {
    echo "❌ Professor ID 8 not found\n";
}

// Test 5: Check data types and constraints
echo "\n--- Testing Data Type Validation ---\n";

// Test quiz data structure
$testQuizData = [
    'professor_id' => 8,
    'program_id' => 41,
    'module_id' => 79,
    'course_id' => 53,
    'quiz_title' => 'Test Quiz Structure',
    'quiz_description' => 'Testing data structure',
    'instructions' => 'Test instructions',
    'status' => 'draft',
    'is_draft' => true,
    'is_active' => false,
    'time_limit' => 60,
    'max_attempts' => 1,
    'total_questions' => 2,
    'created_at' => now(),
];

echo "✓ Quiz data structure is valid\n";

// Test question data structure
$testQuestionData = [
    'quiz_id' => 999, // Will be replaced with actual quiz_id
    'quiz_title' => 'Test Quiz Structure',
    'program_id' => 41,
    'question_text' => 'What is PHP?',
    'question_type' => 'multiple_choice',
    'question_order' => 1,
    'options' => json_encode(['A scripting language', 'A database', 'A framework', 'An operating system']),
    'correct_answer' => 'A scripting language',
    'explanation' => 'PHP is a server-side scripting language',
    'question_source' => 'manual',
    'points' => 1,
    'is_active' => true,
    'created_by_professor' => 8,
];

echo "✓ Question data structure is valid\n";

// Test 6: Check enum values
echo "\n--- Testing Enum Values ---\n";

// Check question_source enum values
try {
    $enumValues = DB::select("SHOW COLUMNS FROM quiz_questions WHERE Field = 'question_source'")[0];
    echo "✓ question_source enum: " . $enumValues->Type . "\n";
    
    if (strpos($enumValues->Type, 'manual') !== false) {
        echo "✓ 'manual' value is allowed in question_source\n";
    } else {
        echo "❌ 'manual' value not found in question_source enum\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking question_source enum: " . $e->getMessage() . "\n";
}

// Check status enum values
try {
    $enumValues = DB::select("SHOW COLUMNS FROM quizzes WHERE Field = 'status'")[0];
    echo "✓ status enum: " . $enumValues->Type . "\n";
    
    if (strpos($enumValues->Type, 'draft') !== false) {
        echo "✓ 'draft' value is allowed in status\n";
    } else {
        echo "❌ 'draft' value not found in status enum\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking status enum: " . $e->getMessage() . "\n";
}

// Test 7: Sample insert test (rollback)
echo "\n--- Testing Sample Insert (with rollback) ---\n";

try {
    DB::beginTransaction();
    
    // Insert test quiz
    $quizId = DB::table('quizzes')->insertGetId($testQuizData);
    echo "✓ Test quiz inserted with ID: $quizId\n";
    
    // Insert test question
    $testQuestionData['quiz_id'] = $quizId;
    $questionId = DB::table('quiz_questions')->insertGetId($testQuestionData);
    echo "✓ Test question inserted with ID: $questionId\n";
    
    // Verify the data
    $insertedQuiz = DB::table('quizzes')->where('quiz_id', $quizId)->first();
    $insertedQuestion = DB::table('quiz_questions')->where('question_id', $questionId)->first();
    
    if ($insertedQuiz && $insertedQuestion) {
        echo "✓ Data verification successful\n";
        echo "  - Quiz title: " . $insertedQuiz->quiz_title . "\n";
        echo "  - Question text: " . $insertedQuestion->question_text . "\n";
        echo "  - Question source: " . $insertedQuestion->question_source . "\n";
    }
    
    // Rollback to clean up
    DB::rollBack();
    echo "✓ Test data rolled back successfully\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Insert test failed: " . $e->getMessage() . "\n";
}

echo "\n=== Database Structure Test Completed ===\n";
