<?php
echo "Checking database for batches...\n";

try {
    require_once 'vendor/autoload.php';
    
    // Load Laravel
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Http\Kernel');
    $kernel->bootstrap();
    
    // Check StudentBatch model directly
    $batches = \App\Models\StudentBatch::with('program')->get();
    echo "Total batches found: " . $batches->count() . "\n";
    
    foreach ($batches as $batch) {
        echo "Batch ID: {$batch->batch_id}\n";
        echo "Batch Name: {$batch->batch_name}\n";
        echo "Program: " . ($batch->program ? $batch->program->program_name : 'N/A') . "\n";
        echo "Status: {$batch->batch_status}\n";
        echo "Capacity: {$batch->current_capacity}/{$batch->max_capacity}\n";
        echo "Start Date: {$batch->start_date}\n";
        echo "---\n";
    }
    
    // Check for program ID 1 specifically
    echo "\nBatches for program ID 1:\n";
    $program1Batches = \App\Models\StudentBatch::where('program_id', 1)
        ->whereIn('batch_status', ['available', 'ongoing'])
        ->get();
    
    echo "Found " . $program1Batches->count() . " batches for program 1\n";
    
    foreach ($program1Batches as $batch) {
        echo "- {$batch->batch_name} ({$batch->batch_status})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
