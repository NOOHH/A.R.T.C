<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== Detailed Batch Analysis ===\n";
$batches = \App\Models\StudentBatch::with('program')->get();
foreach($batches as $batch) {
    $programName = $batch->program ? $batch->program->program_name : 'Unknown';
    echo "ID: {$batch->batch_id} | Name: {$batch->batch_name} | Program: {$programName} (ID: {$batch->program_id}) | Status: {$batch->batch_status} | Capacity: {$batch->current_capacity}/{$batch->max_capacity}\n";
}

echo "\n=== Testing Culinary Program Specifically ===\n";
$culinaryBatches = \App\Models\StudentBatch::where('program_id', 33)
    ->whereIn('batch_status', ['available', 'ongoing'])
    ->get();

echo "Culinary batches found: " . $culinaryBatches->count() . "\n";
foreach($culinaryBatches as $batch) {
    echo "  - Batch ID: {$batch->batch_id} | Name: {$batch->batch_name} | Status: {$batch->batch_status}\n";
    echo "    Start: {$batch->start_date} | End: {$batch->end_date} | Deadline: {$batch->registration_deadline}\n";
    echo "    Capacity: {$batch->current_capacity}/{$batch->max_capacity}\n";
}
?>
