<?php
/**
 * Fix Existing Quiz Attempts Data
 * Updates existing quiz attempts to use correct student_id format
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING EXISTING QUIZ ATTEMPTS DATA ===\n\n";

try {
    // Check existing attempts
    echo "1. Checking existing quiz attempts...\n";
    $attempts = DB::table('quiz_attempts')->get();
    echo "   Found " . $attempts->count() . " quiz attempts\n";
    
    if ($attempts->count() > 0) {
        echo "\n2. Analyzing existing attempts...\n";
        foreach ($attempts as $attempt) {
            echo "   Attempt ID: {$attempt->attempt_id}, Student ID: '{$attempt->student_id}' (type: " . gettype($attempt->student_id) . ")\n";
            
            // Check if this student_id exists in students table
            $student = DB::table('students')->where('student_id', $attempt->student_id)->first();
            if ($student) {
                echo "     ✓ Student found: {$student->student_id}\n";
            } else {
                echo "     ✗ Student not found - needs fixing\n";
                
                // Try to find student by user_id if student_id is numeric
                if (is_numeric($attempt->student_id)) {
                    $studentByUserId = DB::table('students')->where('user_id', $attempt->student_id)->first();
                    if ($studentByUserId) {
                        echo "     → Found student by user_id: {$studentByUserId->student_id}\n";
                        
                        // Update the attempt with correct student_id
                        DB::table('quiz_attempts')
                            ->where('attempt_id', $attempt->attempt_id)
                            ->update(['student_id' => $studentByUserId->student_id]);
                        
                        echo "     ✓ Updated attempt with correct student_id\n";
                    } else {
                        echo "     ✗ No student found with user_id {$attempt->student_id}\n";
                        
                        // Delete orphaned attempt
                        DB::table('quiz_attempts')->where('attempt_id', $attempt->attempt_id)->delete();
                        echo "     ✓ Deleted orphaned attempt\n";
                    }
                }
            }
        }
    }
    
    // Verify the fixes
    echo "\n3. Verifying fixes...\n";
    $fixedAttempts = DB::table('quiz_attempts')->get();
    echo "   Remaining attempts: " . $fixedAttempts->count() . "\n";
    
    foreach ($fixedAttempts as $attempt) {
        $student = DB::table('students')->where('student_id', $attempt->student_id)->first();
        if ($student) {
            echo "   ✓ Attempt {$attempt->attempt_id} -> Student {$student->student_id}\n";
        } else {
            echo "   ✗ Attempt {$attempt->attempt_id} -> No student found\n";
        }
    }
    
    echo "\n=== FIX COMPLETE ===\n";
    echo "Existing quiz attempts have been updated with correct student_id values.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 