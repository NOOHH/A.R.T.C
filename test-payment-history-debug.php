<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PAYMENT HISTORY DEBUG ===\n";

try {
    // Check total enrollments
    $totalEnrollments = App\Models\Enrollment::count();
    echo "Total enrollments: $totalEnrollments\n";
    
    // Check paid enrollments
    $paidEnrollments = App\Models\Enrollment::where('payment_status', 'paid')->count();
    echo "Paid enrollments: $paidEnrollments\n";
    
    // Check payment history records
    $paymentHistoryCount = App\Models\PaymentHistory::count();
    echo "Payment history records: $paymentHistoryCount\n";
    
    // Show some sample paid enrollments with relationships
    echo "\n=== PAID ENROLLMENTS DETAILS ===\n";
    $paidEnrollmentDetails = App\Models\Enrollment::with(['student.user', 'user', 'program', 'package'])
                                                 ->where('payment_status', 'paid')
                                                 ->limit(5)
                                                 ->get();
    
    foreach ($paidEnrollmentDetails as $enrollment) {
        echo "Enrollment ID: {$enrollment->enrollment_id}\n";
        echo "  User ID: {$enrollment->user_id}\n";
        echo "  Student ID: {$enrollment->student_id}\n";
        echo "  Payment Status: {$enrollment->payment_status}\n";
        
        // Check user relationship
        if ($enrollment->user) {
            echo "  User Name: {$enrollment->user->user_name}\n";
        } elseif ($enrollment->student && $enrollment->student->user) {
            echo "  Student User Name: {$enrollment->student->user->user_name}\n";
        } else {
            echo "  No user relationship found\n";
        }
        
        // Check program
        if ($enrollment->program) {
            echo "  Program: {$enrollment->program->program_name}\n";
        } else {
            echo "  No program relationship found\n";
        }
        
        // Check package
        if ($enrollment->package) {
            echo "  Package: {$enrollment->package->package_name}\n";
        } else {
            echo "  No package relationship found\n";
        }
        
        echo "  Created: {$enrollment->created_at}\n";
        echo "  Updated: {$enrollment->updated_at}\n";
        echo "---\n";
    }
    
    // Check all enrollment statuses
    echo "\n=== ENROLLMENT STATUSES ===\n";
    $statuses = App\Models\Enrollment::select('payment_status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                                    ->groupBy('payment_status')
                                    ->get();
    
    foreach ($statuses as $status) {
        echo "Status: {$status->payment_status} - Count: {$status->count}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
