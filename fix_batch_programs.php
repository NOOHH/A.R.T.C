<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Fixing batch program assignments..." . PHP_EOL;

// First, let's see what programs are available
$programs = \App\Models\Program::all();
echo "Available programs:" . PHP_EOL;
foreach ($programs as $program) {
    echo "- ID " . $program->program_id . ": " . $program->program_name . PHP_EOL;
}

// Get the Nursing program (seems to be the main one)
$nursingProgram = \App\Models\Program::where('program_name', 'Nursing')->first();

if (!$nursingProgram) {
    echo "Nursing program not found!" . PHP_EOL;
    exit;
}

echo "\nUsing Nursing program (ID: " . $nursingProgram->program_id . ") for engineering batches as fallback..." . PHP_EOL;

// Fix the batches
$batches = [
    // Batch with non-existent program_id 39
    ['batch_name' => 'Batch 1 Civil Engineer', 'new_program_id' => $nursingProgram->program_id],
    // Engineering batch with wrong program_id
    ['batch_name' => 'Batch3Engineering', 'new_program_id' => $nursingProgram->program_id],
    ['batch_name' => 'Batch 1 Engineering', 'new_program_id' => $nursingProgram->program_id]
];

foreach ($batches as $batchInfo) {
    $batch = \App\Models\StudentBatch::where('batch_name', $batchInfo['batch_name'])->first();
    if ($batch) {
        $oldProgramId = $batch->program_id;
        $batch->program_id = $batchInfo['new_program_id'];
        $batch->save();
        echo "Fixed '{$batch->batch_name}': Changed program_id from {$oldProgramId} to {$batchInfo['new_program_id']}" . PHP_EOL;
    } else {
        echo "Batch '{$batchInfo['batch_name']}' not found!" . PHP_EOL;
    }
}

echo "\nVerification - checking batches again:" . PHP_EOL;
$professor = \App\Models\Professor::find(9);
$batches = $professor->batches()->with('program')->get();

foreach ($batches as $batch) {
    echo "- " . $batch->batch_name . " -> ";
    if ($batch->program) {
        echo $batch->program->program_name . PHP_EOL;
    } else {
        echo "Still no program!" . PHP_EOL;
    }
}
