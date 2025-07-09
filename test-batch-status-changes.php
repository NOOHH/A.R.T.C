<?php
require_once 'vendor/autoload.php';

// Initialize Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StudentBatch;
use App\Models\Enrollment;

echo "Testing Batch Status Management System\n";
echo "=====================================\n\n";

// Test 1: Get a batch and show current students
$batch = StudentBatch::with(['enrollments.user', 'enrollments.student', 'program'])->first();

if (!$batch) {
    echo "No batches found in the system.\n";
    exit;
}

echo "Testing with Batch: {$batch->batch_name}\n";
echo "Max Capacity: {$batch->max_capacity}\n";
echo "Current Capacity (DB): {$batch->current_capacity}\n";
echo "Current Capacity (Dynamic): {$batch->current_capacity}\n\n";

// Get enrollments for this batch
$enrollments = $batch->enrollments;

echo "Total Enrollments in Batch: " . $enrollments->count() . "\n\n";

if ($enrollments->count() > 0) {
    echo "Enrollment Details:\n";
    echo "===================\n";
    
    foreach ($enrollments as $enrollment) {
        $studentName = '';
        if ($enrollment->user) {
            $studentName = trim(($enrollment->user->user_firstname ?? '') . ' ' . ($enrollment->user->user_lastname ?? ''));
        } elseif ($enrollment->student) {
            $studentName = trim(($enrollment->student->firstname ?? '') . ' ' . ($enrollment->student->lastname ?? ''));
        }
        
        // Determine status
        $isPending = false;
        $isCurrent = false;
        
        // Pending conditions
        if (($enrollment->enrollment_status === 'pending' && $enrollment->payment_status === 'pending') ||
            ($enrollment->enrollment_status === 'approved' && $enrollment->payment_status === 'pending') ||
            ($enrollment->enrollment_status === 'pending' && $enrollment->payment_status === 'paid')) {
            $isPending = true;
        }
        
        // Current condition
        if ($enrollment->enrollment_status === 'approved' && $enrollment->payment_status === 'paid') {
            $isCurrent = true;
        }
        
        $status = $isCurrent ? 'CURRENT' : ($isPending ? 'PENDING' : 'UNKNOWN');
        
        echo "ID: {$enrollment->enrollment_id} | Name: {$studentName} | Registration: {$enrollment->enrollment_status} | Payment: {$enrollment->payment_status} | Status: {$status}\n";
    }
    
    echo "\nCounts:\n";
    echo "-------\n";
    $currentCount = $enrollments->filter(function($e) {
        return $e->enrollment_status === 'approved' && $e->payment_status === 'paid';
    })->count();
    
    $pendingCount = $enrollments->filter(function($e) {
        return ($e->enrollment_status === 'pending' && $e->payment_status === 'pending') ||
               ($e->enrollment_status === 'approved' && $e->payment_status === 'pending') ||
               ($e->enrollment_status === 'pending' && $e->payment_status === 'paid');
    })->count();
    
    echo "Current Students: {$currentCount}\n";
    echo "Pending Students: {$pendingCount}\n";
    echo "Available Slots: " . max(0, $batch->max_capacity - $currentCount) . "\n";
} else {
    echo "No enrollments found for this batch.\n";
}

echo "\nTest completed successfully!\n";
?>
