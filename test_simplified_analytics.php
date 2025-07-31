<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "Testing simplified analytics queries...\n";

try {
    // 1. Test Board Passers (simplified)
    echo "1. Testing Board Passers...\n";
    $boardPassers = DB::table('board_passers')
        ->select([
            'student_id',
            'student_name',
            'program',
            'board_exam',
            'exam_date',
            'result',
            'rating'
        ])
        ->where('result', 'PASS')
        ->limit(5)
        ->get();
    
    echo "Board passers found: " . count($boardPassers) . "\n";
    foreach ($boardPassers as $passer) {
        echo "  - {$passer->student_name} | {$passer->board_exam} | {$passer->result} | Rating: {$passer->rating}\n";
    }
    
    // 2. Test Recently Completed
    echo "\n2. Testing Recently Completed...\n";
    $recentlyCompleted = DB::table('enrollments')
        ->join('students', 'enrollments.student_id', '=', 'students.student_id')
        ->join('users', 'students.user_id', '=', 'users.user_id')
        ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
        ->select([
            'users.user_firstname',
            'users.user_lastname',
            'programs.program_name',
            'enrollments.enrollment_type',
            'enrollments.progress_percentage'
        ])
        ->where('enrollments.progress_percentage', '>=', 90)
        ->orderBy('enrollments.updated_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "Recently completed found: " . count($recentlyCompleted) . "\n";
    foreach ($recentlyCompleted as $completion) {
        $name = trim(($completion->user_firstname ?? '') . ' ' . ($completion->user_lastname ?? ''));
        echo "  - {$name} | {$completion->program_name} | {$completion->progress_percentage}% | {$completion->enrollment_type}\n";
    }
    
    // 3. Test Recent Payments
    echo "\n3. Testing Recent Payments...\n";
    $recentPayments = DB::table('payments')
        ->join('enrollments', 'payments.enrollment_id', '=', 'enrollments.enrollment_id')
        ->join('students', 'enrollments.student_id', '=', 'students.student_id')
        ->join('users', 'students.user_id', '=', 'users.user_id')
        ->select([
            'users.user_firstname',
            'users.user_lastname',
            'payments.amount',
            'payments.payment_method',
            'payments.payment_status',
            'payments.created_at'
        ])
        ->whereIn('payments.payment_status', ['paid', 'approved', 'verified'])
        ->orderBy('payments.created_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "Recent payments found: " . count($recentPayments) . "\n";
    foreach ($recentPayments as $payment) {
        $name = trim(($payment->user_firstname ?? '') . ' ' . ($payment->user_lastname ?? ''));
        echo "  - {$name} | \${$payment->amount} | {$payment->payment_status} | {$payment->payment_method}\n";
    }
    
    echo "\n✅ All queries working! The analytics should now display data properly.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
