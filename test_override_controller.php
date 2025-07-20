<?php

// Test AdminOverrideController functionality
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminOverrideController;

echo "Testing AdminOverrideController functionality...\n\n";

try {
    $controller = new AdminOverrideController();
    
    // Test 1: Get status for a module
    echo "1. Testing getStatus for module:\n";
    $response = $controller->getStatus('module', 40);
    $data = json_decode($response->getContent(), true);
    if ($data['success']) {
        echo "   ✓ Module status retrieved successfully\n";
        echo "   - Locked: " . ($data['status']['is_locked'] ? 'Yes' : 'No') . "\n";
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
    }
    
    // Test 2: Get status for a course  
    echo "\n2. Testing getStatus for course:\n";
    $response = $controller->getStatus('course', 1);
    $data = json_decode($response->getContent(), true);
    if ($data['success']) {
        echo "   ✓ Course status retrieved successfully\n";
        echo "   - Locked: " . ($data['status']['is_locked'] ? 'Yes' : 'No') . "\n";
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
    }
    
    // Test 3: Get status for content
    echo "\n3. Testing getStatus for content:\n";
    $response = $controller->getStatus('content', 9);
    $data = json_decode($response->getContent(), true);
    if ($data['success']) {
        echo "   ✓ Content status retrieved successfully\n";
        echo "   - Locked: " . ($data['status']['is_locked'] ? 'Yes' : 'No') . "\n";
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
    }
    
    // Test 4: Get prerequisites
    echo "\n4. Testing getPrerequisites:\n";
    $response = $controller->getPrerequisites();
    $data = json_decode($response->getContent(), true);
    if ($data['success']) {
        echo "   ✓ Prerequisites retrieved successfully\n";
        echo "   - Found " . count($data['prerequisites']) . " prerequisite items\n";
        
        // Show first few
        $counter = 0;
        foreach ($data['prerequisites'] as $prereq) {
            if ($counter >= 3) break;
            echo "   - {$prereq['type']}: {$prereq['name']} (ID: {$prereq['id']})\n";
            $counter++;
        }
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
    }
    
    echo "\n✅ AdminOverrideController tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
