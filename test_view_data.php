<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentBatch;
use App\Models\Professor;

echo "Testing Blade View Data\n";
echo "======================\n\n";

// Simulate what the controller sends to the view
$batches = StudentBatch::with(['program', 'professors'])->get();
$professors = Professor::where('professor_archived', 0)->get();

echo "Batches:\n";
foreach ($batches as $batch) {
    echo "- {$batch->batch_name} (ID: {$batch->batch_id})\n";
    echo "  Professors assigned: " . $batch->professors->count() . "\n";
    foreach ($batch->professors as $prof) {
        echo "    * {$prof->professor_name} (ID: {$prof->professor_id})\n";
    }
    echo "\n";
}

echo "Available Professors:\n";
foreach ($professors as $professor) {
    echo "- {$professor->professor_name} (ID: {$professor->professor_id})\n";
}

echo "\nTesting specific batch (ID: 9):\n";
$targetBatch = $batches->where('batch_id', 9)->first();
if ($targetBatch) {
    echo "Batch name: {$targetBatch->batch_name}\n";
    echo "Assigned professors: " . $targetBatch->professors->count() . "\n";
    
    $assignedProfessorIds = $targetBatch->professors->pluck('professor_id')->toArray();
    echo "Assigned professor IDs: ";
    print_r($assignedProfessorIds);
    
    echo "\nChecking if professor 8 is in assigned list:\n";
    $isAssigned = in_array(8, $assignedProfessorIds);
    echo "Professor 8 is " . ($isAssigned ? "assigned" : "not assigned") . "\n";
}

echo "\nTest completed.\n";
