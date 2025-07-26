<?php

// Check current session and authentication
echo "Checking session and authentication...\n";

// Include Laravel bootstrap
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Session;

try {
    // Start session
    session_start();
    
    echo "Session ID: " . session_id() . "\n";
    echo "Session data: " . json_encode($_SESSION) . "\n";
    
    // Check if professor is logged in via Laravel session
    $professorId = session('professor_id');
    echo "Professor ID from session: " . ($professorId ?? 'not set') . "\n";
    
    if ($professorId) {
        $professor = \App\Models\Professor::find($professorId);
        if ($professor) {
            echo "✓ Professor found: " . $professor->professor_first_name . " " . $professor->professor_last_name . "\n";
            echo "✓ Professor email: " . $professor->professor_email . "\n";
        } else {
            echo "✗ Professor not found in database\n";
        }
    } else {
        echo "✗ No professor logged in\n";
        echo "Available session keys: " . implode(', ', array_keys($_SESSION)) . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
