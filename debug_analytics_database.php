<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "=== DATABASE ANALYSIS FOR ANALYTICS ISSUES ===\n\n";

try {
    // Check board_passers table structure and data
    echo "1. BOARD PASSERS TABLE:\n";
    if (DB::getSchemaBuilder()->hasTable('board_passers')) {
        $columns = DB::getSchemaBuilder()->getColumnListing('board_passers');
        echo "Columns: " . implode(', ', $columns) . "\n";
        
        $passers = DB::table('board_passers')->get();
        echo "Records count: " . $passers->count() . "\n";
        
        if ($passers->count() > 0) {
            echo "Sample data:\n";
            foreach ($passers->take(3) as $passer) {
                echo "  - " . json_encode($passer) . "\n";
            }
        }
    } else {
        echo "❌ board_passers table does NOT exist\n";
    }
    
    echo "\n2. STUDENTS TABLE:\n";
    if (DB::getSchemaBuilder()->hasTable('students')) {
        $students = DB::table('students')->get();
        echo "Students count: " . $students->count() . "\n";
        
        if ($students->count() > 0) {
            echo "Sample students:\n";
            foreach ($students->take(3) as $student) {
                echo "  - ID: {$student->student_id}, User ID: " . ($student->user_id ?? 'N/A') . "\n";
            }
        }
    } else {
        echo "❌ students table does NOT exist\n";
    }
    
    echo "\n3. MODULE COMPLETIONS (for recently completed):\n";
    if (DB::getSchemaBuilder()->hasTable('module_completions')) {
        $columns = DB::getSchemaBuilder()->getColumnListing('module_completions');
        echo "Columns: " . implode(', ', $columns) . "\n";
        
        $completions = DB::table('module_completions')->get();
        echo "Completions count: " . $completions->count() . "\n";
        
        if ($completions->count() > 0) {
            echo "Sample completions:\n";
            foreach ($completions->take(3) as $completion) {
                echo "  - " . json_encode($completion) . "\n";
            }
        }
    } else {
        echo "❌ module_completions table does NOT exist\n";
    }
    
    echo "\n4. ENROLLMENTS TABLE:\n";
    if (DB::getSchemaBuilder()->hasTable('enrollments')) {
        $enrollments = DB::table('enrollments')->get();
        echo "Enrollments count: " . $enrollments->count() . "\n";
        
        if ($enrollments->count() > 0) {
            echo "Sample enrollments:\n";
            foreach ($enrollments->take(3) as $enrollment) {
                echo "  - Student ID: {$enrollment->student_id}, Status: " . ($enrollment->enrollment_status ?? 'N/A') . "\n";
            }
        }
    } else {
        echo "❌ enrollments table does NOT exist\n";
    }
    
    echo "\n5. PROGRESS-RELATED TABLES:\n";
    $progressTables = ['student_progress', 'course_progress', 'module_progress', 'student_module_progress'];
    foreach ($progressTables as $table) {
        if (DB::getSchemaBuilder()->hasTable($table)) {
            $count = DB::table($table)->count();
            echo "✅ {$table}: {$count} records\n";
            
            if ($count > 0) {
                $columns = DB::getSchemaBuilder()->getColumnListing($table);
                echo "   Columns: " . implode(', ', $columns) . "\n";
                
                $sample = DB::table($table)->first();
                echo "   Sample: " . json_encode($sample) . "\n";
            }
        } else {
            echo "❌ {$table}: does not exist\n";
        }
    }
    
    echo "\n6. USERS TABLE (for student names):\n";
    if (DB::getSchemaBuilder()->hasTable('users')) {
        $students = DB::table('users')->where('role', 'student')->get();
        echo "Student users count: " . $students->count() . "\n";
        
        if ($students->count() > 0) {
            echo "Sample student users:\n";
            foreach ($students->take(3) as $user) {
                echo "  - ID: {$user->user_id}, Name: " . ($user->user_firstname ?? 'N/A') . " " . ($user->user_lastname ?? 'N/A') . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
