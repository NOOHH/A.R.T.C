<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Professor;
use App\Models\StudentBatch;
use App\Models\ClassMeeting;
use Illuminate\Support\Facades\DB;

echo "=== Testing Meeting Creation ===\n";

$professor = Professor::first();
$professorId = $professor->professor_id;

echo "Professor ID: " . $professorId . "\n";

// Get professor's batches
$batches = StudentBatch::where('professor_id', $professorId)->get();
echo "Available batches: " . $batches->count() . "\n";

if ($batches->count() > 0) {
    $batch = $batches->first();
    echo "Testing with batch: " . $batch->batch_name . " (ID: " . $batch->batch_id . ")\n";
    
    // Test meeting creation
    try {
        $meeting = ClassMeeting::create([
            'professor_id' => $professorId,
            'batch_id' => $batch->batch_id,
            'title' => 'Test Meeting',
            'meeting_date' => now()->addDay(),
            'meeting_url' => 'https://zoom.us/test',
            'description' => 'This is a test meeting',
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'created_by' => 1 // Default to admin ID 1
        ]);
        
        echo "✓ Meeting created successfully!\n";
        echo "  Meeting ID: " . $meeting->meeting_id . "\n";
        echo "  Title: " . $meeting->title . "\n";
        echo "  Date: " . $meeting->meeting_date . "\n";
        echo "  Status: " . $meeting->status . "\n";
        
        // Test retrieval
        $retrievedMeeting = ClassMeeting::with(['batch.program'])->find($meeting->meeting_id);
        echo "  Batch: " . $retrievedMeeting->batch->batch_name . "\n";
        echo "  Program: " . $retrievedMeeting->batch->program->program_name . "\n";
        
        // Clean up - delete the test meeting
        $meeting->delete();
        echo "✓ Test meeting cleaned up\n";
        
    } catch (Exception $e) {
        echo "✗ Meeting creation failed: " . $e->getMessage() . "\n";
        echo "Error details:\n";
        echo $e->getTraceAsString() . "\n";
    }
} else {
    echo "No batches available for testing\n";
}

echo "\n=== Test Complete ===\n";
