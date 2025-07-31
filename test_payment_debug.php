<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Payment Data ===\n";

try {
    $payments = DB::table('payments')
        ->select(['student_id', 'amount', 'created_at', 'payment_status'])
        ->whereIn('payment_status', ['paid', 'approved', 'verified'])
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    echo "Found " . $payments->count() . " payments\n";
    
    foreach ($payments as $payment) {
        echo "Payment: Student " . $payment->student_id . " - $" . $payment->amount . " on " . $payment->created_at . "\n";
        
        // Test student lookup
        $student = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.user_id')
            ->where('students.student_id', $payment->student_id)
            ->select('users.user_firstname', 'users.user_lastname')
            ->first();
        
        if ($student) {
            echo "  Student Name: " . $student->user_firstname . " " . $student->user_lastname . "\n";
        } else {
            echo "  Student Name: Not found\n";
        }
        
        // Test program lookup via enrollments
        $enrollment = DB::table('enrollments')
            ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
            ->where('enrollments.student_id', $payment->student_id)
            ->select('programs.program_name')
            ->orderBy('enrollments.created_at', 'desc')
            ->first();
        
        if ($enrollment && $enrollment->program_name) {
            echo "  Program: " . $enrollment->program_name . "\n";
        } else {
            echo "  Program: Not found\n";
        }
        echo "---\n";
    }
    
    // Check tables structure
    echo "\n=== Checking Tables ===\n";
    echo "Students table exists: " . (DB::getSchemaBuilder()->hasTable('students') ? 'Yes' : 'No') . "\n";
    echo "Users table exists: " . (DB::getSchemaBuilder()->hasTable('users') ? 'Yes' : 'No') . "\n";
    echo "Enrollments table exists: " . (DB::getSchemaBuilder()->hasTable('enrollments') ? 'Yes' : 'No') . "\n";
    echo "Programs table exists: " . (DB::getSchemaBuilder()->hasTable('programs') ? 'Yes' : 'No') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
