<?php
/*
 * Batch Capacity Fix Script
 * Recalculates and fixes all batch capacity mismatches
 */

require_once 'vendor/autoload.php';

// Laravel Bootstrap
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== BATCH CAPACITY FIX SCRIPT ===\n\n";

    // Get all batches
    $batches = DB::table('student_batches')
        ->where('batch_status', '!=', 'completed')
        ->get();

    echo "Found {$batches->count()} active batches to check.\n\n";

    foreach ($batches as $batch) {
        echo "Checking Batch {$batch->batch_id}: {$batch->batch_name}\n";
        echo "  Current stored capacity: {$batch->current_capacity}/{$batch->max_capacity}\n";

        // Calculate actual capacity based on approved and paid enrollments
        $actualCapacity = DB::table('enrollments')
            ->where('batch_id', $batch->batch_id)
            ->where('enrollment_status', 'approved')
            ->where('payment_status', 'paid')
            ->count();

        echo "  Actual enrolled count: {$actualCapacity}\n";

        if ($actualCapacity != $batch->current_capacity) {
            echo "  ⚠️  MISMATCH DETECTED! Updating...\n";
            
            DB::table('student_batches')
                ->where('batch_id', $batch->batch_id)
                ->update(['current_capacity' => $actualCapacity]);
            
            echo "  ✅ Updated batch {$batch->batch_id} capacity: {$batch->current_capacity} → {$actualCapacity}\n";
        } else {
            echo "  ✅ Capacity is correct\n";
        }
        
        echo "\n";
    }

    echo "=== BATCH CAPACITY FIX COMPLETE ===\n";

} catch (Exception $e) {
    echo "❌ BATCH CAPACITY FIX FAILED: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
