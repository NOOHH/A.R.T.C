<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DATABASE STRUCTURE CHECK ===\n\n";

try {
    // Check students table structure
    echo "1. Students table structure:\n";
    $studentColumns = DB::select("SHOW COLUMNS FROM students");
    foreach ($studentColumns as $column) {
        if ($column->Field === 'student_id') {
            echo "   student_id: {$column->Type} {$column->Null} {$column->Key}\n";
        }
    }
    
    // Check quiz_attempts table structure
    echo "\n2. Quiz_attempts table structure:\n";
    $attemptColumns = DB::select("SHOW COLUMNS FROM quiz_attempts");
    foreach ($attemptColumns as $column) {
        if ($column->Field === 'student_id') {
            echo "   student_id: {$column->Type} {$column->Null} {$column->Key}\n";
        }
    }
    
    // Check actual data
    echo "\n3. Sample data:\n";
    $student = DB::table('students')->first();
    if ($student) {
        echo "   Student ID: '{$student->student_id}' (type: " . gettype($student->student_id) . ")\n";
    }
    
    $attempt = DB::table('quiz_attempts')->first();
    if ($attempt) {
        echo "   QuizAttempt student_id: '{$attempt->student_id}' (type: " . gettype($attempt->student_id) . ")\n";
    }
    
    // Check if there's a data type conversion happening
    echo "\n4. Data type analysis:\n";
    if ($student && $attempt) {
        if (gettype($student->student_id) !== gettype($attempt->student_id)) {
            echo "   ✗ DATA TYPE MISMATCH DETECTED!\n";
            echo "   - Students.student_id: " . gettype($student->student_id) . "\n";
            echo "   - QuizAttempts.student_id: " . gettype($attempt->student_id) . "\n";
        } else {
            echo "   ✓ Data types match\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 