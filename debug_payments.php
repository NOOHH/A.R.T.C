<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "Debugging payment query...\n";

// Check if the JOINs work step by step
echo "1. Direct payments query:\n";
$payments = DB::table('payments')->where('payment_status', 'paid')->count();
echo "Payments with 'paid' status: $payments\n";

echo "\n2. Check enrollment_id in payments:\n";
$sample = DB::table('payments')->where('payment_status', 'paid')->first();
if ($sample) {
    echo "Sample payment enrollment_id: {$sample->enrollment_id}\n";
    
    // Check if this enrollment exists
    $enrollment = DB::table('enrollments')->where('enrollment_id', $sample->enrollment_id)->first();
    if ($enrollment) {
        echo "Enrollment found: student_id = {$enrollment->student_id}\n";
        
        // Check if this student exists
        $student = DB::table('students')->where('student_id', $enrollment->student_id)->first();
        if ($student) {
            echo "Student found: user_id = {$student->user_id}\n";
            
            // Check if this user exists
            $user = DB::table('users')->where('user_id', $student->user_id)->first();
            if ($user) {
                echo "User found: {$user->user_firstname} {$user->user_lastname}\n";
            } else {
                echo "User NOT found\n";
            }
        } else {
            echo "Student NOT found\n";
        }
    } else {
        echo "Enrollment NOT found\n";
    }
}

echo "\n3. Testing simplified payment query:\n";
$payments = DB::table('payments')
    ->select([
        'payments.payment_id',
        'payments.enrollment_id',
        'payments.student_id',
        'payments.amount',
        'payments.payment_status',
        'payments.payment_method'
    ])
    ->where('payments.payment_status', 'paid')
    ->limit(3)
    ->get();

echo "Found " . count($payments) . " payments\n";
foreach ($payments as $payment) {
    echo "Payment ID: {$payment->payment_id}, Student ID: {$payment->student_id}, Amount: {$payment->amount}\n";
}
?>
