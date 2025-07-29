<?php

// Test script to check session and professor authentication
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Professor;

echo "Testing Session and Professor Authentication...\n\n";

// Check current session
echo "1. Current Session State:\n";
echo "session('logged_in'): " . (session('logged_in') ? 'true' : 'false') . "\n";
echo "session('professor_id'): " . session('professor_id') . "\n";
echo "session('user_role'): " . session('user_role') . "\n";
echo "session('user_type'): " . session('user_type') . "\n";
echo "session('user_id'): " . session('user_id') . "\n\n";

// Try to set session manually for testing
echo "2. Setting session manually for testing...\n";
session(['logged_in' => true]);
session(['professor_id' => 8]);
session(['user_role' => 'professor']);
session(['user_type' => 'professor']);
session(['user_id' => 8]);

echo "Session set. Checking again:\n";
echo "session('logged_in'): " . (session('logged_in') ? 'true' : 'false') . "\n";
echo "session('professor_id'): " . session('professor_id') . "\n";
echo "session('user_role'): " . session('user_role') . "\n\n";

// Check if professor exists
echo "3. Checking if Professor ID 8 exists:\n";
$professor = Professor::find(8);
if ($professor) {
    echo "Professor found: " . $professor->professor_name . "\n";
    echo "Professor ID: " . $professor->professor_id . "\n";
    
    // Check assigned programs
    $assignedPrograms = $professor->assignedPrograms()->get();
    echo "Assigned programs: " . $assignedPrograms->count() . "\n";
    foreach ($assignedPrograms as $program) {
        echo "- " . $program->program_name . " (ID: " . $program->program_id . ")\n";
    }
} else {
    echo "Professor ID 8 not found!\n";
}

echo "\nTest completed.\n";
?> 