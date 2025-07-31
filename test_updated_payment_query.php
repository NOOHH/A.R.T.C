<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "Testing updated payment query...\n";

try {
    $recentPayments = DB::table('payments')
        ->join('students', 'payments.student_id', '=', 'students.student_id')
        ->join('users', 'students.user_id', '=', 'users.user_id')
        ->leftJoin('enrollments', 'payments.enrollment_id', '=', 'enrollments.enrollment_id')
        ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
        ->select([
            'users.user_firstname',
            'users.user_lastname',
            'students.student_id',
            'programs.program_name',
            'payments.amount',
            'payments.payment_method',
            'payments.created_at',
            'payments.payment_status'
        ])
        ->whereIn('payments.payment_status', ['paid', 'approved', 'verified'])
        ->orderBy('payments.created_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "Recent payments found: " . count($recentPayments) . "\n";
    foreach ($recentPayments as $payment) {
        $name = trim(($payment->user_firstname ?? '') . ' ' . ($payment->user_lastname ?? ''));
        $program = $payment->program_name ?? 'Unknown Program';
        echo "  - {$name} | \${$payment->amount} | {$payment->payment_status} | {$payment->payment_method} | {$program}\n";
    }
    
    echo "\n✅ Payment query is now working!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
