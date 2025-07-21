<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentBatch;
use App\Models\Professor;

echo "Testing Professor Assignment\n";
echo "============================\n\n";

// Find the batch
$batch = StudentBatch::find(9);
echo "Found batch: " . $batch->batch_name . " (ID: " . $batch->batch_id . ")\n";

// Find the professor
$professor = Professor::find(8);
echo "Found professor: " . $professor->professor_name . " (ID: " . $professor->professor_id . ")\n\n";

// Test current professors
echo "Current professors for this batch:\n";
$currentProfessors = $batch->professors;
if ($currentProfessors->count() > 0) {
    foreach ($currentProfessors as $prof) {
        echo "- " . $prof->professor_name . " (ID: " . $prof->professor_id . ")\n";
    }
} else {
    echo "- No professors assigned\n";
}

echo "\nAssigning professor to batch...\n";

// Assign professor
$batch->professors()->sync([8]);

echo "Assignment completed. Checking result...\n\n";

// Refresh and check again
$batch->refresh();
$updatedProfessors = $batch->professors;
echo "Updated professors for this batch:\n";
if ($updatedProfessors->count() > 0) {
    foreach ($updatedProfessors as $prof) {
        echo "- " . $prof->professor_name . " (ID: " . $prof->professor_id . ")\n";
    }
} else {
    echo "- No professors assigned (assignment failed)\n";
}

echo "\nChecking pivot table directly:\n";
$pivotRecords = \DB::table('professor_batch')->where('batch_id', 9)->get();
foreach ($pivotRecords as $record) {
    echo "- Batch ID: " . $record->batch_id . ", Professor ID: " . $record->professor_id . "\n";
}

echo "\nTest completed.\n";
