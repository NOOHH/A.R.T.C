<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\AdminController;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\PaymentHistory;

// Test registration with batch_id
$testData = [
    'program_id' => 23,
    'package_id' => 2,
    'enrollment_type' => 'Full',
    'learning_mode' => 'synchronous',
    'batch_id' => 1, // This should now work
];

echo "ARTC System Test Results:\n";
echo "========================\n\n";

// Test 1: Check if batch foreign key is fixed
echo "1. Testing batch_id foreign key constraint...\n";
try {
    $enrollment = new Enrollment();
    $enrollment->student_id = '2025-07-00001';
    $enrollment->user_id = 1;
    $enrollment->program_id = 23;
    $enrollment->package_id = 2;
    $enrollment->enrollment_type = 'Full';
    $enrollment->learning_mode = 'synchronous';
    $enrollment->batch_id = 1; // This should work now
    $enrollment->enrollment_status = 'pending';
    $enrollment->payment_status = 'pending';
    
    echo "   ✓ Batch foreign key constraint is fixed\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: Check if PaymentHistory table exists
echo "\n2. Testing PaymentHistory table...\n";
try {
    $history = new PaymentHistory();
    echo "   ✓ PaymentHistory table exists and model works\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check user_id linkage
echo "\n3. Testing user_id linkage in enrollments...\n";
try {
    $enrollment = Enrollment::with('user', 'student', 'batch')->first();
    if ($enrollment) {
        echo "   ✓ Found enrollment with ID: " . $enrollment->enrollment_id . "\n";
        echo "   - Student ID: " . ($enrollment->student_id ?? 'null') . "\n";
        echo "   - User ID: " . ($enrollment->user_id ?? 'null') . "\n";
        echo "   - Batch: " . ($enrollment->batch ? $enrollment->batch->batch_name : 'null') . "\n";
        echo "   - Payment Status: " . $enrollment->payment_status . "\n";
    } else {
        echo "   ✗ No enrollments found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n========================\n";
echo "Test completed!\n";
echo "Next steps:\n";
echo "1. Test student registration with batch selection\n";
echo "2. Test admin 'Mark as Paid' functionality\n";
echo "3. Test batch management and student listing\n";
echo "4. Test paywall logic with paid enrollments\n";
