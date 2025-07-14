<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== Testing Culinary Program Batch Filtering ===\n";

// Test the exact query used in the controller
$programId = 33; // Culinary
echo "Program ID: $programId\n";

// Step 1: Check raw batches
$rawBatches = \App\Models\StudentBatch::where('program_id', $programId)->get();
echo "Raw batches for program: " . $rawBatches->count() . "\n";

// Step 2: Check with status filter
$statusFiltered = \App\Models\StudentBatch::where('program_id', $programId)
    ->whereIn('batch_status', ['available', 'ongoing'])
    ->get();
echo "After status filter: " . $statusFiltered->count() . "\n";

// Step 3: Check with deadline filter (old logic)
$oldLogic = \App\Models\StudentBatch::where('program_id', $programId)
    ->whereIn('batch_status', ['available', 'ongoing'])
    ->where(function($query) {
        $query->where('registration_deadline', '>=', now())
              ->orWhere('batch_status', 'ongoing');
    })
    ->get();
echo "With old deadline filter: " . $oldLogic->count() . "\n";

// Step 4: Check with new logic
$newLogic = \App\Models\StudentBatch::where('program_id', $programId)
    ->whereIn('batch_status', ['available', 'ongoing'])
    ->where(function($query) {
        $query->where(function($subQuery) {
            $subQuery->where('batch_status', 'available')
                     ->where('registration_deadline', '>=', now());
        })->orWhere('batch_status', 'ongoing');
    })
    ->get();
echo "With new deadline filter: " . $newLogic->count() . "\n";

// Step 5: Check capacity filter
$capacityFiltered = $newLogic->filter(function($batch) {
    return $batch->current_capacity < $batch->max_capacity;
});
echo "After capacity filter: " . $capacityFiltered->count() . "\n";

// Show details of the Culinary batch
echo "\n=== Culinary Batch Details ===\n";
$culinaryBatch = \App\Models\StudentBatch::where('program_id', 33)->first();
if ($culinaryBatch) {
    echo "Name: {$culinaryBatch->batch_name}\n";
    echo "Status: {$culinaryBatch->batch_status}\n";
    echo "Current time: " . now() . "\n";
    echo "Registration deadline: {$culinaryBatch->registration_deadline}\n";
    echo "Deadline passed: " . (now() > $culinaryBatch->registration_deadline ? 'Yes' : 'No') . "\n";
    echo "Capacity: {$culinaryBatch->current_capacity}/{$culinaryBatch->max_capacity}\n";
    echo "Has available slots: " . ($culinaryBatch->current_capacity < $culinaryBatch->max_capacity ? 'Yes' : 'No') . "\n";
}
?>
