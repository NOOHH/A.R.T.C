<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check professor-batch relationships
$professor = \App\Models\Professor::find(9); // Based on the error showing professor_id 9
if ($professor) {
    echo "Professor: " . $professor->professor_name . PHP_EOL;
    
    $batches = $professor->batches()->get();
    echo "Professor's batches: " . $batches->count() . PHP_EOL;
    
    foreach ($batches as $batch) {
        echo "- " . $batch->batch_name . " (Program ID: " . $batch->program_id . ")" . PHP_EOL;
        
        $program = \App\Models\Program::find($batch->program_id);
        if ($program) {
            echo "  Program: " . $program->program_name . PHP_EOL;
        } else {
            echo "  Program: NOT FOUND!" . PHP_EOL;
            
            // Try to find a matching program and suggest fix
            $matchingProgram = \App\Models\Program::where('program_name', 'LIKE', '%' . $batch->batch_name . '%')->first();
            if (!$matchingProgram) {
                // Try to extract program name from batch name
                if (strpos($batch->batch_name, 'Engineering') !== false) {
                    $matchingProgram = \App\Models\Program::where('program_name', 'LIKE', '%Engineering%')->first();
                } elseif (strpos($batch->batch_name, 'Nursing') !== false) {
                    $matchingProgram = \App\Models\Program::where('program_name', 'LIKE', '%Nursing%')->first();
                } elseif (strpos($batch->batch_name, 'Culinary') !== false) {
                    $matchingProgram = \App\Models\Program::where('program_name', 'LIKE', '%Culinary%')->first();
                }
            }
            
            if ($matchingProgram) {
                echo "  Suggested fix: Update program_id to " . $matchingProgram->program_id . " (" . $matchingProgram->program_name . ")" . PHP_EOL;
            }
        }
    }
}
