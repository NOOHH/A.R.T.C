#!/usr/bin/env php
<?php
/*
 * Database Structure Check
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DATABASE STRUCTURE CHECK ===\n";

try {
    $tables = ['students', 'registrations', 'enrollments'];
    
    foreach ($tables as $table) {
        echo "\n--- Table: $table ---\n";
        $columns = DB::select("DESCRIBE $table");
        
        foreach ($columns as $column) {
            echo sprintf("%-30s %-15s %s\n", 
                $column->Field, 
                $column->Type, 
                $column->Null === 'YES' ? 'NULL' : 'NOT NULL'
            );
        }
    }
    
    echo "\n=== FILE COLUMNS IN STUDENTS TABLE ===\n";
    $studentColumns = DB::select("DESCRIBE students");
    $fileColumns = array_filter($studentColumns, function($col) {
        return strpos($col->Field, 'good_moral') !== false || 
               strpos($col->Field, 'PSA') !== false ||
               strpos($col->Field, 'Course_Cert') !== false ||
               strpos($col->Field, 'TOR') !== false ||
               strpos($col->Field, 'Cert_of_Grad') !== false ||
               strpos($col->Field, 'photo') !== false ||
               strpos($col->Field, 'certificate') !== false ||
               strpos($col->Field, 'clearance') !== false ||
               strpos($col->Field, 'diploma') !== false ||
               strpos($col->Field, 'transcript') !== false ||
               strpos($col->Field, 'birth_cert') !== false ||
               strpos($col->Field, 'valid_id') !== false;
    });
    
    if (count($fileColumns) > 0) {
        echo "File-related columns found:\n";
        foreach ($fileColumns as $col) {
            echo "  - {$col->Field} ({$col->Type})\n";
        }
    } else {
        echo "No file columns found in students table!\n";
    }
    
    echo "\n=== RECENT STUDENT DATA ===\n";
    $students = DB::table('students')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    foreach ($students as $student) {
        echo "Student ID: {$student->student_id}\n";
        echo "Name: {$student->firstname} {$student->lastname}\n";
        echo "Education Level: {$student->education_level}\n";
        
        // Check file fields
        $fileFields = ['good_moral', 'PSA', 'Course_Cert', 'TOR', 'Cert_of_Grad', 'photo_2x2'];
        foreach ($fileFields as $field) {
            $value = $student->{$field} ?? 'NULL';
            echo "  $field: $value\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "=== DONE ===\n";
