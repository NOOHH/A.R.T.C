<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "Setting up test data for analytics...\n";

try {
    // 1. Update some enrollments to have higher progress for Recently Completed
    echo "1. Updating enrollment progress...\n";
    
    $updated = DB::table('enrollments')
        ->where('progress_percentage', '<', 90)
        ->limit(3)
        ->update([
            'progress_percentage' => rand(90, 100),
            'certificate_eligible' => 1,
            'updated_at' => now()
        ]);
    
    echo "Updated $updated enrollments with high progress\n";
    
    // 2. Check if we have board passers data
    echo "2. Checking board passers...\n";
    $boardPassersCount = DB::table('board_passers')->count();
    echo "Board passers in database: $boardPassersCount\n";
    
    if ($boardPassersCount > 0) {
        $sample = DB::table('board_passers')->first();
        echo "Sample board passer: {$sample->student_name} - {$sample->result}\n";
    }
    
    // 3. Check payments
    echo "3. Checking payments...\n";
    $paymentsCount = DB::table('payments')->where('payment_status', 'paid')->count();
    echo "Paid payments in database: $paymentsCount\n";
    
    if ($paymentsCount > 0) {
        $sample = DB::table('payments')
            ->join('enrollments', 'payments.enrollment_id', '=', 'enrollments.enrollment_id')
            ->join('students', 'enrollments.student_id', '=', 'students.student_id')
            ->join('users', 'students.user_id', '=', 'users.user_id')
            ->where('payments.payment_status', 'paid')
            ->select('users.user_firstname', 'users.user_lastname', 'payments.amount', 'payments.payment_status')
            ->first();
        
        if ($sample) {
            echo "Sample payment: {$sample->user_firstname} {$sample->user_lastname} - \${$sample->amount} - {$sample->payment_status}\n";
        }
    }
    
    // 4. Let's test the actual queries our controller uses
    echo "\n4. Testing actual controller queries...\n";
    
    // Test Board Passers query
    $boardPassers = DB::table('board_passers')
        ->leftJoin('students', 'board_passers.student_id', '=', 'students.student_id')
        ->leftJoin('users', 'students.user_id', '=', 'users.user_id')
        ->leftJoin('enrollments', 'students.student_id', '=', 'enrollments.student_id')
        ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
        ->select([
            'board_passers.student_id',
            'board_passers.student_name',
            'users.user_firstname',
            'users.user_lastname',
            'programs.program_name',
            'board_passers.board_exam',
            'board_passers.exam_date',
            'board_passers.result',
            'board_passers.rating'
        ])
        ->where('board_passers.result', 'PASS')
        ->limit(5)
        ->get();
    
    echo "Board passers query result: " . count($boardPassers) . " records\n";
    if (!empty($boardPassers)) {
        foreach ($boardPassers as $passer) {
            $fullName = $passer->student_name ?? 
                       trim(($passer->user_firstname ?? '') . ' ' . ($passer->user_lastname ?? ''));
            echo "  - {$fullName} | {$passer->board_exam} | {$passer->result}\n";
        }
    }
    
    // Test Recently Completed query
    $recentlyCompleted = DB::table('enrollments')
        ->join('students', 'enrollments.student_id', '=', 'students.student_id')
        ->join('users', 'students.user_id', '=', 'users.user_id')
        ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
        ->select([
            'users.user_firstname',
            'users.user_lastname',
            'users.email',
            'students.student_id',
            'programs.program_name',
            'enrollments.enrollment_type',
            'enrollments.progress_percentage'
        ])
        ->where(function($q) {
            $q->where('enrollments.enrollment_status', 'completed')
              ->orWhere('enrollments.progress_percentage', '>=', 90)
              ->orWhere('enrollments.certificate_issued', 1)
              ->orWhere('enrollments.certificate_eligible', 1);
        })
        ->orderBy('enrollments.updated_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "Recently completed query result: " . count($recentlyCompleted) . " records\n";
    if (!empty($recentlyCompleted)) {
        foreach ($recentlyCompleted as $completion) {
            $name = trim(($completion->user_firstname ?? '') . ' ' . ($completion->user_lastname ?? ''));
            echo "  - {$name} | {$completion->program_name} | {$completion->progress_percentage}%\n";
        }
    }
    
    // Test Recent Payments query
    $recentPayments = DB::table('payments')
        ->join('enrollments', 'payments.enrollment_id', '=', 'enrollments.enrollment_id')
        ->join('students', 'enrollments.student_id', '=', 'students.student_id')
        ->join('users', 'students.user_id', '=', 'users.user_id')
        ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
        ->select([
            'users.user_firstname',
            'users.user_lastname',
            'students.student_id',
            'programs.program_name',
            'payments.amount',
            'payments.payment_method',
            'payments.created_at as payment_date',
            'payments.payment_status'
        ])
        ->where('payments.payment_status', '!=', 'failed')
        ->orderBy('payments.created_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "Recent payments query result: " . count($recentPayments) . " records\n";
    if (!empty($recentPayments)) {
        foreach ($recentPayments as $payment) {
            $name = trim(($payment->user_firstname ?? '') . ' ' . ($payment->user_lastname ?? ''));
            echo "  - {$name} | \${$payment->amount} | {$payment->payment_status}\n";
        }
    }
    
    echo "\n✅ Analytics test data setup complete!\n";
    echo "Now you can visit http://127.0.0.1:8000/admin/analytics to see the results.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
