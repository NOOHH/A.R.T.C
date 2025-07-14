<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== Testing getBatchesByProgram API Endpoint ===\n";

// Test for both programs that user should see
$testPrograms = [
    32 => 'Engineer',
    33 => 'Culinary', 
    34 => 'Nursing'
];

foreach($testPrograms as $programId => $programName) {
    echo "\n--- Testing Program: $programName (ID: $programId) ---\n";
    
    $controller = new \App\Http\Controllers\StudentRegistrationController();
    $request = new \Illuminate\Http\Request(['program_id' => $programId]);
    
    $response = $controller->getBatchesByProgram($request);
    $data = json_decode($response->getContent(), true);
    
    echo "Raw response: " . $response->getContent() . "\n";
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Data type: " . gettype($data) . "\n";
    
    if (is_array($data)) {
        echo "Batches count: " . count($data) . "\n";
        foreach($data as $batch) {
            if (isset($batch['batch_name'])) {
                echo "  - {$batch['batch_name']} ({$batch['batch_status']}) - Available slots: " . 
                     ($batch['available_slots'] ?? 'N/A') . "\n";
            }
        }
    } else {
        echo "Data is not an array: " . print_r($data, true) . "\n";
    }
}

echo "\n=== Checking Actual Database Batches ===\n";
$allBatches = \App\Models\StudentBatch::with('program')
    ->whereIn('batch_status', ['available', 'ongoing'])
    ->get();

foreach($allBatches as $batch) {
    echo "DB Batch: {$batch->batch_name} | Program: {$batch->program->program_name} | Status: {$batch->batch_status}\n";
}
?>
