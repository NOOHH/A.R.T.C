<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Program;
use App\Models\StudentBatch;
use Illuminate\Support\Facades\DB;

echo "=== Testing Auto-Batch Creation for Programs Without Available Batches ===" . PHP_EOL;

try {
    // Get all programs
    $programs = Program::select('program_id', 'program_name')->get();
    
    echo "Checking all programs for auto-batch creation scenario..." . PHP_EOL;
    
    foreach ($programs as $program) {
        // Check for available batches
        $availableBatches = StudentBatch::where('program_id', $program->program_id)
            ->where(function($query) {
                $query->where('batch_status', 'available')
                      ->orWhere(function($q) {
                          $q->where('batch_status', 'ongoing')
                            ->whereRaw('current_capacity < max_capacity');
                      });
            })
            ->where('registration_deadline', '>=', now()->toDateString())
            ->count();
        
        echo "Program: {$program->program_name} (ID: {$program->program_id}) - Available batches: {$availableBatches}" . PHP_EOL;
        
        if ($availableBatches === 0) {
            echo "  ✓ This program would trigger AUTO-BATCH CREATION" . PHP_EOL;
            
            // Simulate creating a pending batch
            $batchCount = StudentBatch::where('program_id', $program->program_id)->count() + 1;
            echo "  ✓ Would create: '{$program->program_name} Batch {$batchCount}' with status 'pending'" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "=== Testing Batch Creation API Response ===" . PHP_EOL;
    
    // Find a program with no available batches, or temporarily simulate one
    $testProgram = $programs->first();
    
    echo "Testing API response format for program: {$testProgram->program_name}" . PHP_EOL;
    
    // Simulate the API response when no batches are available
    $response_no_batches = [
        'success' => true,
        'message' => 'No active batches available. A new batch will be created for you.',
        'batches' => [],
        'auto_create' => true
    ];
    
    echo "API Response when no batches available:" . PHP_EOL;
    echo json_encode($response_no_batches, JSON_PRETTY_PRINT) . PHP_EOL;
    
    // Simulate the API response when batches are available
    $response_with_batches = [
        'success' => true,
        'batches' => [
            [
                'batch_id' => 1,
                'batch_name' => 'Sample Batch 1',
                'batch_status' => 'available',
                'start_date' => '2025-01-15',
                'current_capacity' => 5,
                'max_capacity' => 10
            ]
        ],
        'auto_create' => false
    ];
    
    echo PHP_EOL . "API Response when batches are available:" . PHP_EOL;
    echo json_encode($response_with_batches, JSON_PRETTY_PRINT) . PHP_EOL;
    
    echo PHP_EOL . "=== Test Completed ===" . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

?>
