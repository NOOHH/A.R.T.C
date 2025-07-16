<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Batch Model and API ===\n";

try {
    // Test the Batch model
    echo "1. Testing Batch model:\n";
    $batches = App\Models\Batch::select('batch_id as id', 'batch_name as name')
        ->orderBy('batch_name')
        ->get();
    
    echo "Found " . $batches->count() . " batches:\n";
    foreach($batches as $batch) {
        echo "  ID: {$batch->id}, Name: {$batch->name}\n";
    }
    
    echo "\n2. Converting to JSON (for API response):\n";
    echo json_encode($batches, JSON_PRETTY_PRINT) . "\n";
    
    echo "\n✅ Batch API should now work correctly!\n";

} catch (Exception $e) {
    echo "❌ Error testing batches: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
