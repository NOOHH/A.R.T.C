<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Professors Table ===\n";

// Check if professors table exists
try {
    $columns = DB::select('DESCRIBE professors');
    echo "Professors table structure:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n=== Sample Professors Data ===\n";
    $professors = DB::table('professors')->limit(3)->get();
    echo "Found " . count($professors) . " professors\n\n";
    
    foreach ($professors as $professor) {
        echo "Professor data:\n";
        print_r($professor);
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error accessing professors table: " . $e->getMessage() . "\n";
}

// Check program-professor relationships
echo "\n=== Checking Program-Professor Relationships ===\n";
try {
    $programProfessors = DB::table('professor_program')->limit(5)->get();
    echo "Found " . count($programProfessors) . " program-professor relationships\n\n";
    
    foreach ($programProfessors as $rel) {
        echo "Program ID: {$rel->program_id}, Professor ID: {$rel->professor_id}\n";
    }
} catch (Exception $e) {
    echo "Error accessing professor_program table: " . $e->getMessage() . "\n";
}

// Test with a real program search
echo "\n=== Testing Program Search ===\n";
use App\Models\Program;

$programs = Program::with(['professors'])->limit(3)->get();
echo "Found " . count($programs) . " programs\n\n";

foreach ($programs as $program) {
    echo "Program: {$program->program_name}\n";
    echo "Professors count: " . $program->professors->count() . "\n";
    
    foreach ($program->professors as $prof) {
        echo "  Professor ID: {$prof->professor_id}\n";
        echo "  Professor Type: " . get_class($prof) . "\n";
        
        // Check if there's a user relationship
        try {
            if (method_exists($prof, 'user')) {
                $user = $prof->user;
                echo "  Has user relationship: " . ($user ? 'Yes' : 'No') . "\n";
            } else {
                echo "  No user() method found\n";
            }
        } catch (Exception $e) {
            echo "  Error accessing user: " . $e->getMessage() . "\n";
        }
        
        // Try direct field access
        echo "  Professor name: {$prof->professor_first_name} {$prof->professor_last_name}\n";
    }
    echo "---\n";
}
