<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check the current professor's batches with program data
$professor = \App\Models\Professor::find(9);
if ($professor) {
    echo "Professor: " . $professor->professor_name . PHP_EOL;
    
    $batches = $professor->batches()->with('program')->get();
    echo "Batches with programs:" . PHP_EOL;
    
    foreach ($batches as $batch) {
        echo "- " . $batch->batch_name . PHP_EOL;
        echo "  Program ID: " . $batch->program_id . PHP_EOL;
        if ($batch->program) {
            echo "  Program Name: " . $batch->program->program_name . PHP_EOL;
            echo "  Program Description: " . $batch->program->program_description . PHP_EOL;
        } else {
            echo "  No program relationship!" . PHP_EOL;
        }
        echo PHP_EOL;
    }
}
