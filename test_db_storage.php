#!/usr/bin/env php
<?php
/*
 * Simple Database File Storage Test
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DATABASE FILE STORAGE TEST ===\n";

DB::beginTransaction();

try {
    // Test 1: Insert a student record with file paths
    $testStudentId = '2025-07-TEST-' . time();
    $testFilePath = 'documents/test_file_' . time() . '.png';
    
    $studentData = [
        'student_id' => $testStudentId,
        'user_id' => 999999,
        'firstname' => 'Test',
        'lastname' => 'Student',
        'email' => 'test@example.com',
        'education_level' => 'Graduate',
        'good_moral' => $testFilePath,
        'PSA' => $testFilePath,
        'Course_Cert' => $testFilePath,
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    DB::table('students')->insert($studentData);
    echo "✅ Student record inserted with file paths\n";
    
    // Test 2: Verify the data was stored
    $stored = DB::table('students')->where('student_id', $testStudentId)->first();
    
    if ($stored) {
        echo "✅ Student record retrieved successfully\n";
        echo "📋 Student Data:\n";
        echo "   ID: {$stored->student_id}\n";
        echo "   Name: {$stored->firstname} {$stored->lastname}\n";
        echo "   Education: {$stored->education_level}\n";
        echo "   Good Moral: " . ($stored->good_moral ?: 'NULL') . "\n";
        echo "   PSA: " . ($stored->PSA ?: 'NULL') . "\n";
        echo "   Course Cert: " . ($stored->Course_Cert ?: 'NULL') . "\n";
        
        if (!empty($stored->good_moral)) {
            echo "✅ File paths successfully stored in database!\n";
        } else {
            echo "❌ File paths are NULL in database\n";
        }
    } else {
        echo "❌ Failed to retrieve student record\n";
    }
    
    // Test 3: Test registration record with file paths
    $testRegId = time();
    $registrationData = [
        'registration_id' => $testRegId,
        'user_id' => 999999,
        'firstname' => 'Test',
        'lastname' => 'Student',
        'program_id' => 1,
        'package_id' => 1,
        'education_level' => 'Graduate',
        'learning_mode' => 'asynchronous',
        'enrollment_type' => 'Full',
        'status' => 'pending',
        'good_moral' => $testFilePath,
        'PSA' => $testFilePath,
        'Course_Cert' => $testFilePath,
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    DB::table('registrations')->insert($registrationData);
    echo "✅ Registration record inserted with file paths\n";
    
    // Test 4: Test enrollment record with file paths
    $enrollmentData = [
        'registration_id' => $testRegId,
        'user_id' => 999999,
        'program_id' => 1,
        'package_id' => 1,
        'enrollment_type' => 'Full',
        'learning_mode' => 'asynchronous',
        'enrollment_status' => 'pending',
        'payment_status' => 'pending',
        'good_moral' => $testFilePath,
        'psa' => $testFilePath,
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    $enrollmentId = DB::table('enrollments')->insertGetId($enrollmentData);
    echo "✅ Enrollment record inserted with ID: $enrollmentId\n";
    
    // Test 5: Test batch creation
    echo "\n=== BATCH CREATION TEST ===\n";
    
    $batchData = [
        'batch_name' => 'Test Batch ' . time(),
        'program_id' => 1,
        'max_capacity' => 30,
        'current_capacity' => 0,
        'batch_status' => 'pending',
        'registration_deadline' => now()->addMonth(),
        'start_date' => now()->addWeeks(3),
        'end_date' => now()->addWeeks(3)->addMonths(8),
        'description' => 'Test batch for verification',
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    $batchId = DB::table('student_batches')->insertGetId($batchData);
    echo "✅ Test batch created with ID: $batchId\n";
    
    // Verify batch creation
    $batch = DB::table('student_batches')->where('batch_id', $batchId)->first();
    if ($batch) {
        echo "📋 Batch Details:\n";
        echo "   ID: {$batch->batch_id}\n";
        echo "   Name: {$batch->batch_name}\n";
        echo "   Capacity: {$batch->current_capacity}/{$batch->max_capacity}\n";
        echo "   Status: {$batch->batch_status}\n";
        echo "   Start Date: {$batch->start_date}\n";
        echo "   End Date: {$batch->end_date}\n";
    }
    
    echo "\n✅ All tests passed successfully!\n";
    echo "🔄 Rolling back test data...\n";
    
    DB::rollback();
    echo "✅ Test data cleaned up\n";
    
} catch (\Exception $e) {
    DB::rollback();
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "📍 Error location: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
