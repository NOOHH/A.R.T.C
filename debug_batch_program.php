<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the batch data with program relationship
$batch = \App\Models\StudentBatch::with('program')->first();
if ($batch) {
    echo "Batch Name: " . $batch->batch_name . PHP_EOL;
    echo "Program ID: " . $batch->program_id . PHP_EOL;
    echo "Program exists: " . ($batch->program ? 'Yes' : 'No') . PHP_EOL;
    if ($batch->program) {
        echo "Program Title: " . $batch->program->program_title . PHP_EOL;
        echo "Program Name: " . $batch->program->program_name . PHP_EOL;
    } else {
        echo "Program relationship is null" . PHP_EOL;
        
        // Check if the program exists separately
        $program = \App\Models\Program::find($batch->program_id);
        if ($program) {
            echo "Program found separately: " . $program->program_title . PHP_EOL;
        } else {
            echo "Program not found in database" . PHP_EOL;
        }
    }
    
    echo "Raw batch data:" . PHP_EOL;
    print_r($batch->toArray());
} else {
    echo "No batches found" . PHP_EOL;
}

// Check all programs
$programs = \App\Models\Program::all();
echo "\nTotal programs: " . $programs->count() . PHP_EOL;
foreach ($programs as $program) {
    echo "Program " . $program->program_id . ": " . $program->program_title . " / " . $program->program_name . PHP_EOL;
}

// Check all batches
$batches = \App\Models\StudentBatch::all();
echo "\nTotal batches: " . $batches->count() . PHP_EOL;
foreach ($batches as $batch) {
    echo "Batch " . $batch->batch_id . ": " . $batch->batch_name . " (Program ID: " . $batch->program_id . ")" . PHP_EOL;
}
