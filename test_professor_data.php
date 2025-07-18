<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Professor;
use App\Models\StudentBatch;
use App\Models\ClassMeeting;

echo "=== Testing Professor Meeting Data ===\n";

// Test 1: Check professors
$professors = Professor::all();
echo "Total Professors: " . $professors->count() . "\n";

if ($professors->count() > 0) {
    $professor = $professors->first();
    echo "Testing with Professor: " . $professor->professor_first_name . " " . $professor->professor_last_name . "\n";
    echo "Professor ID: " . $professor->professor_id . "\n";
    
    // Test 2: Check batches assigned to this professor
    $batches = StudentBatch::where('professor_id', $professor->professor_id)->with('program')->get();
    echo "Assigned Batches: " . $batches->count() . "\n";
    
    foreach ($batches as $batch) {
        echo "  - Batch: " . $batch->batch_name . "\n";
        echo "    Program: " . ($batch->program ? $batch->program->program_name : 'No Program') . "\n";
        echo "    Batch ID: " . $batch->batch_id . "\n";
    }
    
    // Test 3: Check professor's programs
    echo "\nProfessor's Programs:\n";
    try {
        $programs = $professor->programs()->get();
        echo "Total Programs: " . $programs->count() . "\n";
        foreach ($programs as $program) {
            echo "  - Program: " . $program->program_name . " (ID: " . $program->program_id . ")\n";
        }
    } catch (Exception $e) {
        echo "Error getting programs: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Check meetings
    $meetings = ClassMeeting::where('professor_id', $professor->professor_id)->get();
    echo "\nMeetings for this professor: " . $meetings->count() . "\n";
    
    // Test 5: Simulate controller data preparation
    echo "\n=== Simulating Controller Logic ===\n";
    
    // Get professor's programs
    $professorPrograms = $professor->programs()->get();
    echo "Professor Programs Count: " . $professorPrograms->count() . "\n";
    
    // Get batches assigned to this professor
    $professorBatches = StudentBatch::with(['program'])
        ->where('professor_id', $professor->professor_id)
        ->get();
    echo "Professor Batches Count: " . $professorBatches->count() . "\n";
    
    // Organize batches by program
    foreach ($professorPrograms as $program) {
        echo "Program: " . $program->program_name . "\n";
        $programBatches = $professorBatches->where('program_id', $program->program_id);
        echo "  Batches in this program: " . $programBatches->count() . "\n";
        
        foreach ($programBatches as $batch) {
            echo "    - " . $batch->batch_name . "\n";
        }
    }
    
} else {
    echo "No professors found in database\n";
}

echo "\n=== Test Complete ===\n";
