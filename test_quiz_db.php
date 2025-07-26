<?php

// Simple test script to check database tables
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Testing Quiz Generator Database Structure...\n\n";

// Check if quizzes table exists and has correct columns
echo "Checking quizzes table:\n";
if (Schema::hasTable('quizzes')) {
    echo "✓ quizzes table exists\n";
    $columns = Schema::getColumnListing('quizzes');
    echo "Columns: " . implode(', ', $columns) . "\n";
    
    $requiredColumns = ['quiz_id', 'professor_id', 'program_id', 'module_id', 'course_id', 'content_id', 'quiz_title', 'is_draft', 'randomize_order', 'tags'];
    foreach ($requiredColumns as $col) {
        if (in_array($col, $columns)) {
            echo "✓ $col column exists\n";
        } else {
            echo "✗ $col column missing\n";
        }
    }
} else {
    echo "✗ quizzes table does not exist\n";
}

echo "\nChecking quiz_questions table:\n";
if (Schema::hasTable('quiz_questions')) {
    echo "✓ quiz_questions table exists\n";
    $columns = Schema::getColumnListing('quiz_questions');
    echo "Columns: " . implode(', ', $columns) . "\n";
} else {
    echo "✗ quiz_questions table does not exist\n";
}

// Test professor existence
echo "\nChecking professors table:\n";
$professorCount = DB::table('professors')->count();
echo "Professor count: $professorCount\n";

if ($professorCount > 0) {
    $professor = DB::table('professors')->first();
    echo "Sample professor ID: {$professor->professor_id}\n";
}

// Test AI Quiz setting
echo "\nChecking AI Quiz setting:\n";
$setting = DB::table('admin_settings')->where('setting_key', 'ai_quiz_enabled')->first();
if ($setting) {
    echo "AI Quiz enabled: {$setting->setting_value}\n";
} else {
    echo "AI Quiz setting not found\n";
}

echo "\nDone!\n";
