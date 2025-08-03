<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Student;
use App\Models\Program;
use App\Models\Package;
use App\Models\StudentBatch;

echo "=== TESTING ENROLLMENT ASSIGNMENT ===\n\n";

// Test data retrieval for enrollment form
echo "1. TESTING DATA FOR ENROLLMENT FORM:\n";

try {
    $students = Student::where('is_archived', false)
        ->whereNotNull('date_approved')
        ->orderBy('firstname')
        ->orderBy('lastname')
        ->get(['student_id', 'firstname', 'lastname', 'email']);
    
    echo "Available Students (" . $students->count() . "):\n";
    foreach ($students as $student) {
        echo "  - {$student->student_id}: {$student->firstname} {$student->lastname} ({$student->email})\n";
    }
    
    $programs = Program::where('is_archived', false)->orderBy('program_name')->get(['program_id', 'program_name']);
    echo "\nAvailable Programs (" . $programs->count() . "):\n";
    foreach ($programs as $program) {
        echo "  - {$program->program_id}: {$program->program_name}\n";
    }
    
    $batches = StudentBatch::where('batch_status', 'available')->orderBy('batch_name')->get(['batch_id', 'batch_name', 'start_date']);
    echo "\nAvailable Batches (" . $batches->count() . "):\n";
    foreach ($batches as $batch) {
        echo "  - {$batch->batch_id}: {$batch->batch_name} (Start: {$batch->start_date})\n";
    }
    
    $packages = Package::limit(5)->get(['package_id', 'package_name', 'program_id']);
    echo "\nAvailable Packages (" . $packages->count() . "):\n";
    foreach ($packages as $package) {
        echo "  - {$package->package_id}: {$package->package_name} (Program: {$package->program_id})\n";
    }
    
} catch (Exception $e) {
    echo "Error retrieving data: " . $e->getMessage() . "\n";
}

echo "\n2. TESTING SAMPLE ENROLLMENT ASSIGNMENT:\n";

try {
    if ($students->count() > 0 && $programs->count() > 0 && $packages->count() > 0) {
        $student = $students->first();
        $program = $programs->first();
        $package = $packages->first();
        
        echo "Attempting to create sample enrollment:\n";
        echo "  Student: {$student->firstname} {$student->lastname} ({$student->student_id})\n";
        echo "  Program: {$program->program_name} ({$program->program_id})\n";
        echo "  Package: {$package->package_name} ({$package->package_id})\n";
        
        // Check if student is already enrolled
        $existingEnrollment = \App\Models\Enrollment::where([
            'student_id' => $student->student_id,
            'program_id' => $program->program_id
        ])->first();
        
        if ($existingEnrollment) {
            echo "  Result: Student is already enrolled in this program\n";
        } else {
            echo "  Result: Student can be enrolled in this program\n";
        }
        
    } else {
        echo "Insufficient data for testing enrollment\n";
    }
    
} catch (Exception $e) {
    echo "Error testing enrollment: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
