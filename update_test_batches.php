<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== Updating Batch Statuses ===\n";

// Update all batches based on current date
$batches = \App\Models\StudentBatch::all();
foreach($batches as $batch) {
    $oldStatus = $batch->batch_status;
    $batch->updateStatusBasedOnDates();
    echo "Batch {$batch->batch_name}: {$oldStatus} -> {$batch->batch_status}\n";
}

echo "\n=== Creating Test Batches with Different Statuses ===\n";

// Create an ongoing batch that started 5 days ago
$ongoingBatch = new \App\Models\StudentBatch();
$ongoingBatch->batch_name = 'Ongoing Test Batch';
$ongoingBatch->program_id = 32; // Engineer program
$ongoingBatch->start_date = now()->subDays(5);
$ongoingBatch->end_date = now()->addMonths(8);
$ongoingBatch->registration_deadline = now()->addDays(30);
$ongoingBatch->max_capacity = 15;
$ongoingBatch->current_capacity = 8;
$ongoingBatch->batch_status = 'ongoing';
$ongoingBatch->description = 'Test batch that has already started';
$ongoingBatch->save();
echo "Created ongoing batch: {$ongoingBatch->batch_name}\n";

// Create a completed batch
$completedBatch = new \App\Models\StudentBatch();
$completedBatch->batch_name = 'Completed Test Batch';
$completedBatch->program_id = 33; // Culinary program (assuming this exists)
$completedBatch->start_date = now()->subMonths(9);
$completedBatch->end_date = now()->subDays(7);
$completedBatch->registration_deadline = now()->subMonths(9)->addDays(30);
$completedBatch->max_capacity = 12;
$completedBatch->current_capacity = 10;
$completedBatch->batch_status = 'completed';
$completedBatch->description = 'Test batch that has completed';
$completedBatch->save();
echo "Created completed batch: {$completedBatch->batch_name}\n";

echo "\n=== Final Status Summary ===\n";
$finalBatches = \App\Models\StudentBatch::all();
foreach(['pending', 'available', 'ongoing', 'completed'] as $status) {
    $count = $finalBatches->where('batch_status', $status)->count();
    echo "$status: $count batches\n";
}
?>
