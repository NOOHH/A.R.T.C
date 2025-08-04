<?php
/**
 * Fix Quiz Attempts Table Structure
 * Changes student_id from integer to string to match students table
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING QUIZ ATTEMPTS TABLE STRUCTURE ===\n\n";

try {
    // Check current structure
    echo "1. Checking current structure...\n";
    $columns = DB::select("SHOW COLUMNS FROM quiz_attempts LIKE 'student_id'");
    if (!empty($columns)) {
        echo "   Current student_id type: {$columns[0]->Type}\n";
    }
    
    // Check if we need to fix it
    if (strpos($columns[0]->Type, 'int') !== false) {
        echo "\n2. Fixing student_id column type...\n";
        
        // Drop existing indexes that might conflict
        echo "   Dropping indexes...\n";
        try {
            DB::statement("ALTER TABLE quiz_attempts DROP INDEX quiz_attempts_quiz_id_student_id_index");
        } catch (Exception $e) {
            echo "   Index already dropped or doesn't exist\n";
        }
        
        try {
            DB::statement("ALTER TABLE quiz_attempts DROP INDEX quiz_attempts_student_id_index");
        } catch (Exception $e) {
            echo "   Index already dropped or doesn't exist\n";
        }
        
        // Change column type
        echo "   Changing student_id to VARCHAR(255)...\n";
        DB::statement("ALTER TABLE quiz_attempts MODIFY COLUMN student_id VARCHAR(255) NOT NULL");
        
        // Recreate indexes
        echo "   Recreating indexes...\n";
        DB::statement("ALTER TABLE quiz_attempts ADD INDEX quiz_attempts_quiz_id_student_id_index (quiz_id, student_id)");
        DB::statement("ALTER TABLE quiz_attempts ADD INDEX quiz_attempts_student_id_index (student_id)");
        
        echo "   ✓ Column type changed successfully\n";
    } else {
        echo "   ✓ Column type is already correct\n";
    }
    
    // Verify the change
    echo "\n3. Verifying the change...\n";
    $newColumns = DB::select("SHOW COLUMNS FROM quiz_attempts LIKE 'student_id'");
    if (!empty($newColumns)) {
        echo "   New student_id type: {$newColumns[0]->Type}\n";
    }
    
    // Test data insertion
    echo "\n4. Testing data insertion...\n";
    $testStudent = DB::table('students')->first();
    if ($testStudent) {
        echo "   Test student ID: {$testStudent->student_id}\n";
        
        // Try to create a test attempt
        $testAttempt = DB::table('quiz_attempts')->insert([
            'quiz_id' => 1,
            'student_id' => $testStudent->student_id,
            'answers' => json_encode([]),
            'total_questions' => 1,
            'status' => 'in_progress',
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        if ($testAttempt) {
            echo "   ✓ Test data insertion successful\n";
            
            // Clean up test data
            DB::table('quiz_attempts')->where('student_id', $testStudent->student_id)->delete();
            echo "   ✓ Test data cleaned up\n";
        } else {
            echo "   ✗ Test data insertion failed\n";
        }
    }
    
    echo "\n=== FIX COMPLETE ===\n";
    echo "The quiz_attempts table structure has been updated.\n";
    echo "The student_id column is now VARCHAR(255) to match the students table.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 