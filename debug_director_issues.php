<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Director;
use Illuminate\Support\Facades\Hash;

echo "=== DIRECTOR ISSUES DEBUGGING ===\n";

try {
    // Test 1: Check current password storage
    echo "\n1. CHECKING PASSWORD STORAGE\n";
    $directors = Director::take(3)->get();
    
    foreach ($directors as $director) {
        echo "   Director: {$director->directors_name}\n";
        echo "   Email: {$director->directors_email}\n";
        echo "   Password (first 20 chars): " . substr($director->directors_password, 0, 20) . "...\n";
        echo "   Is password hashed? " . (strlen($director->directors_password) > 20 && str_starts_with($director->directors_password, '$') ? 'YES' : 'NO') . "\n";
        echo "   Password length: " . strlen($director->directors_password) . "\n\n";
    }
    
    // Test 2: Check program assignments
    echo "\n2. CHECKING PROGRAM ASSIGNMENTS\n";
    foreach ($directors as $director) {
        echo "   Director: {$director->directors_name}\n";
        echo "   Has all program access: " . ($director->has_all_program_access ? 'YES' : 'NO') . "\n";
        echo "   Assigned programs: " . $director->assignedPrograms->count() . "\n";
        
        if ($director->assignedPrograms->count() > 0) {
            foreach ($director->assignedPrograms as $program) {
                echo "     - {$program->program_name} (ID: {$program->program_id})\n";
            }
        }
        echo "\n";
    }
    
    echo "\n=== ISSUES IDENTIFIED ===\n";
    
    // Check for plain text passwords
    $plainTextPasswords = Director::whereRaw('LENGTH(directors_password) < 20')->count();
    $hashedPasswords = Director::whereRaw('LENGTH(directors_password) >= 20')->count();
    
    echo "Directors with plain text passwords: $plainTextPasswords\n";
    echo "Directors with hashed passwords: $hashedPasswords\n";
    
    if ($plainTextPasswords > 0) {
        echo "⚠️  ISSUE: Some directors have plain text passwords!\n";
    }
    
    echo "\n=== RECOMMENDATIONS ===\n";
    echo "1. Fix password encryption in store() method\n";
    echo "2. Check JavaScript for program checkbox conflicts\n";
    echo "3. Verify program assignment logic in update() method\n";
    
} catch (Exception $e) {
    echo "Error during debugging: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
