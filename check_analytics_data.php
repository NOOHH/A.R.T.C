<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "Checking database tables...\n";

// Check if board_passers table exists and has data
try {
    $boardPassers = DB::table('board_passers')->count();
    echo "board_passers table: $boardPassers records\n";
    
    if ($boardPassers > 0) {
        $sample = DB::table('board_passers')->first();
        echo "Sample board passer record:\n";
        print_r($sample);
    }
} catch (Exception $e) {
    echo "board_passers table error: " . $e->getMessage() . "\n";
}

// Check recently completed data
try {
    $completed = DB::table('enrollments')
        ->where('enrollment_status', 'completed')
        ->count();
    echo "Completed enrollments: $completed records\n";
    
    if ($completed > 0) {
        $sample = DB::table('enrollments')
            ->where('enrollment_status', 'completed')
            ->first();
        echo "Sample completed enrollment:\n";
        print_r($sample);
    }
} catch (Exception $e) {
    echo "Completed enrollments error: " . $e->getMessage() . "\n";
}

// Check payment tables
try {
    $payments = DB::table('payments')->count();
    echo "payments table: $payments records\n";
    
    if ($payments > 0) {
        $sample = DB::table('payments')->first();
        echo "Sample payment record:\n";
        print_r($sample);
    }
} catch (Exception $e) {
    echo "payments table error: " . $e->getMessage() . "\n";
    
    // Try payment_history
    try {
        $paymentHistory = DB::table('payment_history')->count();
        echo "payment_history table: $paymentHistory records\n";
        
        if ($paymentHistory > 0) {
            $sample = DB::table('payment_history')->first();
            echo "Sample payment history record:\n";
            print_r($sample);
        }
    } catch (Exception $e2) {
        echo "payment_history table error: " . $e2->getMessage() . "\n";
    }
}

// Check table structures
echo "\nChecking table structures...\n";

$tables = ['board_passers', 'enrollments', 'payments', 'payment_history'];
foreach ($tables as $table) {
    try {
        if (DB::getSchemaBuilder()->hasTable($table)) {
            $columns = DB::getSchemaBuilder()->getColumnListing($table);
            echo "$table columns: " . implode(', ', $columns) . "\n";
        } else {
            echo "$table: Table does not exist\n";
        }
    } catch (Exception $e) {
        echo "$table structure error: " . $e->getMessage() . "\n";
    }
}
?>
