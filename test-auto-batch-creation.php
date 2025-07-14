<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Program;
use App\Models\StudentBatch;
use Illuminate\Support\Facades\DB;

echo "=== Automatic Batch Creation Test ===" . PHP_EOL;

try {
    // Check available programs
    echo "1. Checking available programs..." . PHP_EOL;
    $programs = Program::select('program_id', 'program_name')->limit(3)->get();
    
    if ($programs->isEmpty()) {
        echo "No programs found in database." . PHP_EOL;
        exit;
    }
    
    foreach ($programs as $program) {
        echo "   Program ID: {$program->program_id} - Name: {$program->program_name}" . PHP_EOL;
    }
    
    // Test program (use first available)
    $testProgram = $programs->first();
    echo PHP_EOL . "2. Testing batch creation for program: {$testProgram->program_name} (ID: {$testProgram->program_id})" . PHP_EOL;
    
    // Check existing batches for this program
    $existingBatches = StudentBatch::where('program_id', $testProgram->program_id)
        ->where('batch_status', 'available')
        ->get();
    
    echo "   Existing available batches: " . count($existingBatches) . PHP_EOL;
    
    if ($existingBatches->isEmpty()) {
        echo "   ✓ No available batches found - automatic creation should trigger" . PHP_EOL;
        
        // Test the createPendingBatch functionality
        echo "3. Testing automatic batch creation..." . PHP_EOL;
        
        $batchCount = StudentBatch::where('program_id', $testProgram->program_id)->count() + 1;
        
        $newBatch = StudentBatch::create([
            'batch_name' => $testProgram->program_name . ' Batch ' . $batchCount,
            'program_id' => $testProgram->program_id,
            'max_capacity' => 10,
            'current_capacity' => 1,
            'batch_status' => 'pending',
            'start_date' => now()->addDays(14),
            'end_date' => now()->addDays(14)->addMonths(8),
            'registration_deadline' => now()->addDays(10),
            'description' => 'Auto-created batch for new enrollments. Awaiting admin verification.',
            'created_by' => 1 // Default admin
        ]);
        
        echo "   ✓ Successfully created batch:" . PHP_EOL;
        echo "     ID: {$newBatch->batch_id}" . PHP_EOL;
        echo "     Name: {$newBatch->batch_name}" . PHP_EOL;
        echo "     Status: {$newBatch->batch_status}" . PHP_EOL;
        echo "     Start Date: {$newBatch->start_date}" . PHP_EOL;
        echo "     End Date: {$newBatch->end_date}" . PHP_EOL;
        
    } else {
        echo "   Available batches exist - showing first 3:" . PHP_EOL;
        foreach ($existingBatches->take(3) as $batch) {
            echo "     - {$batch->batch_name} (Status: {$batch->batch_status}, Capacity: {$batch->current_capacity}/{$batch->max_capacity})" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "4. Testing API endpoint..." . PHP_EOL;
    
    // Simulate the API call
    $batches = StudentBatch::where('program_id', $testProgram->program_id)
        ->where(function($query) {
            $query->where('batch_status', 'available')
                  ->orWhere(function($q) {
                      $q->where('batch_status', 'ongoing')
                        ->whereRaw('current_capacity < max_capacity');
                  });
        })
        ->where('registration_deadline', '>=', now()->toDateString())
        ->orderBy('created_at', 'desc')
        ->get();
    
    if ($batches->isEmpty()) {
        echo "   ✓ API would return: auto_create = true" . PHP_EOL;
        echo "   ✓ Message: 'No active batches available. A new batch will be created for you.'" . PHP_EOL;
    } else {
        echo "   Available batches found: " . count($batches) . PHP_EOL;
        echo "   ✓ API would return: auto_create = false" . PHP_EOL;
    }
    
    echo PHP_EOL . "=== Test Completed Successfully ===" . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}

?>
