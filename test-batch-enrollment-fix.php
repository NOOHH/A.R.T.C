<?php
/**
 * Test script to verify batch enrollment and payment marking fixes
 * 
 * This script checks:
 * 1. That enrollments have batch_id properly set
 * 2. That the mark as paid functionality works
 * 
 * Run this after testing the registration flow and admin payment marking
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);

use App\Models\Enrollment;
use App\Models\StudentBatch;

echo "=== BATCH ENROLLMENT AND PAYMENT FIX TEST ===\n\n";

// Test 1: Check if any enrollments have batch_id set
echo "1. Checking enrollments with batch_id...\n";
$enrollmentsWithBatch = Enrollment::whereNotNull('batch_id')->count();
$totalEnrollments = Enrollment::count();

echo "   - Total enrollments: {$totalEnrollments}\n";
echo "   - Enrollments with batch_id: {$enrollmentsWithBatch}\n";

if ($enrollmentsWithBatch > 0) {
    echo "   ✅ GOOD: Some enrollments have batch_id set\n";
    
    // Show sample enrollment with batch
    $sampleEnrollment = Enrollment::with(['batch', 'student', 'program'])
        ->whereNotNull('batch_id')
        ->first();
    
    if ($sampleEnrollment) {
        echo "   Sample enrollment with batch:\n";
        echo "     - Enrollment ID: {$sampleEnrollment->enrollment_id}\n";
        echo "     - Student ID: {$sampleEnrollment->student_id}\n";
        echo "     - Batch ID: {$sampleEnrollment->batch_id}\n";
        echo "     - Batch Name: " . ($sampleEnrollment->batch ? $sampleEnrollment->batch->batch_name : 'N/A') . "\n";
        echo "     - Program: " . ($sampleEnrollment->program ? $sampleEnrollment->program->program_name : 'N/A') . "\n";
    }
} else {
    echo "   ⚠️  WARNING: No enrollments have batch_id set\n";
    echo "   This might be expected if:\n";
    echo "   - No students have enrolled with batch selection yet\n";
    echo "   - All enrollments are older (before the fix)\n";
}

echo "\n";

// Test 2: Check payment statuses
echo "2. Checking payment statuses...\n";
$paymentStatuses = Enrollment::select('payment_status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
    ->groupBy('payment_status')
    ->get();

foreach ($paymentStatuses as $status) {
    echo "   - {$status->payment_status}: {$status->count} enrollments\n";
}

// Test 3: Check available batches
echo "\n3. Checking available batches...\n";
$availableBatches = StudentBatch::with('program')
    ->where('batch_status', '!=', 'closed')
    ->where('registration_deadline', '>=', now())
    ->count();

$totalBatches = StudentBatch::count();
echo "   - Total batches: {$totalBatches}\n";
echo "   - Available batches: {$availableBatches}\n";

if ($availableBatches > 0) {
    echo "   ✅ GOOD: Available batches exist for enrollment\n";
    
    // Show sample available batch
    $sampleBatch = StudentBatch::with('program')
        ->where('batch_status', '!=', 'closed')
        ->where('registration_deadline', '>=', now())
        ->first();
    
    if ($sampleBatch) {
        echo "   Sample available batch:\n";
        echo "     - Batch ID: {$sampleBatch->batch_id}\n";
        echo "     - Batch Name: {$sampleBatch->batch_name}\n";
        echo "     - Program: " . ($sampleBatch->program ? $sampleBatch->program->program_name : 'N/A') . "\n";
        echo "     - Status: {$sampleBatch->batch_status}\n";
        echo "     - Capacity: {$sampleBatch->current_capacity}/{$sampleBatch->max_capacity}\n";
        echo "     - Registration Deadline: {$sampleBatch->registration_deadline}\n";
    }
} else {
    echo "   ⚠️  WARNING: No available batches for enrollment\n";
}

echo "\n=== TEST COMPLETE ===\n\n";

echo "To test the fixes manually:\n";
echo "1. Go to the registration form (/enrollment/full)\n";
echo "2. Select a program that has available batches\n";
echo "3. Select 'Synchronous' learning mode to see batch options\n";
echo "4. Complete registration with a batch selected\n";
echo "5. Check admin panel > Student Registration > Pending\n";
echo "6. Approve the student\n";
echo "7. Check admin panel > Payment > Pending\n";
echo "8. Click 'Mark as Paid' button\n";
echo "9. Verify enrollment now has batch_id and payment_status = 'paid'\n\n";

echo "Expected results:\n";
echo "- ✅ Enrollment should have batch_id set correctly\n";
echo "- ✅ 'Mark as Paid' button should work without errors\n";
echo "- ✅ Payment status should change to 'paid'\n";
echo "- ✅ Student dashboard should show correct batch information\n";
