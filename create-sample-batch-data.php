<?php

// Create sample batch data
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentBatch;
use App\Models\Program;

echo "Creating sample batch data...\n";

try {
    // Check if we have programs first
    $programs = Program::where('is_archived', 0)->get();
    
    if ($programs->count() == 0) {
        echo "No active programs found. Creating a sample program...\n";
        // You might need to create a program first or use an existing one
        $program = Program::create([
            'program_name' => 'Test Program',
            'program_description' => 'Test program for batch enrollment',
            'created_by_admin_id' => 1,
            'is_archived' => 0
        ]);
        $programId = $program->program_id;
    } else {
        $programId = $programs->first()->program_id;
        echo "Using existing program ID: $programId\n";
    }
    
    // Create a sample batch if none exists
    $batchCount = StudentBatch::count();
    echo "Current batch count: $batchCount\n";
    
    if ($batchCount == 0) {
        echo "Creating sample batch...\n";
        StudentBatch::create([
            'batch_name' => 'Test Batch 2025',
            'program_id' => $programId,
            'max_capacity' => 30,
            'current_capacity' => 0,
            'batch_status' => 'available',
            'registration_deadline' => '2025-08-01',
            'start_date' => '2025-08-15',
            'description' => 'Test batch for 2025 enrollment'
        ]);
        echo "Sample batch created successfully!\n";
    } else {
        echo "Batches already exist.\n";
    }
    
    echo "Sample data setup completed!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\nDone.\n";
