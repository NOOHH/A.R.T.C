<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Professor;
use App\Models\StudentBatch;
use App\Models\ClassMeeting;

echo "=== Testing Updated Controller Logic ===\n";

$professor = Professor::first();
$professorId = $professor->professor_id;

echo "Professor: " . $professor->professor_first_name . " " . $professor->professor_last_name . "\n";

// Get batches assigned to this professor
$professorBatches = StudentBatch::with(['program'])
    ->where('professor_id', $professorId)
    ->get();

echo "Professor Batches: " . $professorBatches->count() . "\n";

// Get all programs that have batches assigned to this professor
$programsWithBatches = $professorBatches->pluck('program')->unique('program_id');

echo "Programs with assigned batches: " . $programsWithBatches->count() . "\n";
foreach ($programsWithBatches as $program) {
    echo "  - " . $program->program_name . " (ID: " . $program->program_id . ")\n";
}

// Combine professor's assigned programs with programs that have his batches
$assignedPrograms = $professor->programs()->get();
echo "\nProfessor's assigned programs: " . $assignedPrograms->count() . "\n";
foreach ($assignedPrograms as $program) {
    echo "  - " . $program->program_name . " (ID: " . $program->program_id . ")\n";
}

$allRelevantPrograms = $assignedPrograms->merge($programsWithBatches)->unique('program_id');
echo "\nAll relevant programs: " . $allRelevantPrograms->count() . "\n";
foreach ($allRelevantPrograms as $program) {
    echo "  - " . $program->program_name . " (ID: " . $program->program_id . ")\n";
}

$professorPrograms = collect();

// Organize batches by program
foreach ($allRelevantPrograms as $program) {
    $programBatches = $professorBatches->where('program_id', $program->program_id);
    
    echo "\nProgram: " . $program->program_name . "\n";
    echo "  Batches in this program: " . $programBatches->count() . "\n";
    
    if ($programBatches->count() > 0) {
        $program->batches = $programBatches;
        
        foreach ($program->batches as $batch) {
            echo "    - " . $batch->batch_name . " (ID: " . $batch->batch_id . ")\n";
        }
        
        $professorPrograms->push($program);
    }
}

echo "\nFinal professor programs with batches: " . $professorPrograms->count() . "\n";

// Test meeting creation data
echo "\n=== Meeting Creation Form Data ===\n";
echo "Programs for dropdown: " . $allRelevantPrograms->count() . "\n";
echo "Batches for dropdown: " . $professorBatches->count() . "\n";

echo "\n=== Test Complete ===\n";
