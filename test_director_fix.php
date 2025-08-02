<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Testing Director Query Fix ===\n\n";
    
    // Test the query that was causing the error
    echo "1. Testing Director query with directors_id:\n";
    $director = DB::table('directors')->where('directors_id', 7)->first();
    if ($director) {
        echo "✓ SUCCESS: Found director: " . $director->directors_name . "\n";
        echo "  Email: " . $director->directors_email . "\n";
        echo "  Archived: " . ($director->directors_archived ? 'Yes' : 'No') . "\n";
    } else {
        echo "✗ FAILED: No director found with directors_id = 7\n";
    }
    
    echo "\n2. Testing old query that was failing (director_id):\n";
    try {
        $oldQuery = DB::table('directors')->where('director_id', 7)->first();
        echo "✗ UNEXPECTED: Old query should have failed but didn't\n";
    } catch (Exception $e) {
        echo "✓ EXPECTED: Old query failed as expected: " . substr($e->getMessage(), 0, 100) . "...\n";
    }
    
    echo "\n3. Testing Model-based query:\n";
    try {
        $directorModel = \App\Models\Director::where('directors_id', 7)->first();
        if ($directorModel) {
            echo "✓ SUCCESS: Model query works: " . $directorModel->directors_name . "\n";
        } else {
            echo "✗ FAILED: Model query returned null\n";
        }
    } catch (Exception $e) {
        echo "✗ ERROR in model query: " . $e->getMessage() . "\n";
    }
    
    echo "\n4. Testing admin-sidebar logic:\n";
    // Simulate the session data
    session(['user_type' => 'director', 'user_id' => 7]);
    
    if (session('user_type') === 'director') {
        $adminUser = \App\Models\Director::where('directors_id', session('user_id'))->first();
        $adminName = $adminUser ? $adminUser->directors_name : session('user_name', 'Director');
        echo "✓ SUCCESS: Admin sidebar logic works. Name: " . $adminName . "\n";
    }
    
    echo "\n=== All Tests Complete ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
