<?php
// Test the AdminController paymentHistory method directly

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING ADMIN PAYMENT HISTORY CONTROLLER ===\n";

try {
    // Test the same query as the controller
    $enrollments = App\Models\Enrollment::with(['student.user', 'program', 'package'])
                                      ->where('payment_status', 'paid')
                                      ->orderBy('updated_at', 'desc')
                                      ->get();
    
    echo "Query result count: " . $enrollments->count() . "\n";
    echo "Query result type: " . get_class($enrollments) . "\n";
    
    if ($enrollments->count() > 0) {
        echo "\nFirst enrollment details:\n";
        $first = $enrollments->first();
        echo "- Enrollment ID: " . $first->enrollment_id . "\n";
        echo "- Payment Status: " . $first->payment_status . "\n";
        echo "- User ID: " . $first->user_id . "\n";
        echo "- Student ID: " . $first->student_id . "\n";
        
        // Test relationships
        echo "\nRelationship tests:\n";
        echo "- Student exists: " . ($first->student ? 'YES' : 'NO') . "\n";
        if ($first->student) {
            echo "- Student user exists: " . ($first->student->user ? 'YES' : 'NO') . "\n";
            if ($first->student->user) {
                echo "- Student user name: " . $first->student->user->user_name . "\n";
            }
        }
        
        echo "- Direct user exists: " . ($first->user ? 'YES' : 'NO') . "\n";
        if ($first->user) {
            echo "- Direct user name: " . $first->user->user_name . "\n";
        }
        
        echo "- Program exists: " . ($first->program ? 'YES' : 'NO') . "\n";
        if ($first->program) {
            echo "- Program name: " . $first->program->program_name . "\n";
        }
        
        echo "- Package exists: " . ($first->package ? 'YES' : 'NO') . "\n";
        if ($first->package) {
            echo "- Package name: " . $first->package->package_name . "\n";
        }
    } else {
        echo "No paid enrollments found!\n";
        
        // Check what payment statuses exist
        echo "\nChecking all payment statuses:\n";
        $statuses = App\Models\Enrollment::select('payment_status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                                        ->groupBy('payment_status')
                                        ->get();
        
        foreach ($statuses as $status) {
            echo "- {$status->payment_status}: {$status->count}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
