<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Enrollment;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use Illuminate\Support\Facades\Log;

echo "=== Testing Dashboard Pending Enrollments ===\n\n";

try {
    // Test 1: Check if there are any enrollments with pending status
    echo "1. Checking for pending enrollments:\n";
    $pendingEnrollments = Enrollment::where('enrollment_status', 'pending')->get();
    echo "   Found " . $pendingEnrollments->count() . " pending enrollments\n";
    
    foreach ($pendingEnrollments as $enrollment) {
        echo "   - Enrollment ID: {$enrollment->enrollment_id}\n";
        echo "     User ID: {$enrollment->user_id}\n";
        echo "     Student ID: {$enrollment->student_id}\n";
        echo "     Program: " . ($enrollment->program ? $enrollment->program->program_name : 'No program') . "\n";
        echo "     Status: {$enrollment->enrollment_status}\n";
        echo "     Payment: {$enrollment->payment_status}\n\n";
    }
    
    // Test 2: Check users table for active sessions
    echo "2. Checking users table:\n";
    $users = User::take(5)->get();
    foreach ($users as $user) {
        echo "   - User ID: {$user->user_id}, Name: {$user->user_name}, Email: {$user->user_email}\n";
    }
    echo "\n";
    
    // Test 3: Simulate dashboard logic for a specific user
    if ($pendingEnrollments->count() > 0) {
        $testEnrollment = $pendingEnrollments->first();
        $testUserId = $testEnrollment->user_id;
        
        echo "3. Testing dashboard logic for user_id: {$testUserId}\n";
        
        // Simulate the dashboard controller logic
        $enrollments = collect();
        
        if ($testUserId) {
            $userEnrollments = Enrollment::where('user_id', $testUserId)
                ->with(['program', 'package', 'batch'])
                ->get();
            $enrollments = $enrollments->merge($userEnrollments);
            echo "   Found " . $userEnrollments->count() . " enrollments by user_id\n";
        }
        
        $student = Student::where('user_id', $testUserId)->first();
        if ($student) {
            $studentEnrollments = Enrollment::where('student_id', $student->student_id)
                ->with(['program', 'package', 'batch'])
                ->get();
            $enrollments = $enrollments->merge($studentEnrollments);
            echo "   Found " . $studentEnrollments->count() . " additional enrollments by student_id\n";
        }
        
        $enrollments = $enrollments->unique('enrollment_id');
        echo "   Total unique enrollments: " . $enrollments->count() . "\n";
        
        foreach ($enrollments as $enrollment) {
            echo "   - {$enrollment->program->program_name} - Status: {$enrollment->enrollment_status}\n";
        }
    }
    
    // Test 4: Check if programs have valid data
    echo "\n4. Checking programs table:\n";
    $programs = Program::take(3)->get();
    foreach ($programs as $program) {
        echo "   - Program ID: {$program->program_id}, Name: {$program->program_name}\n";
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
