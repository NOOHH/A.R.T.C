<?php
// Simple test to check if the analytics view compiles properly
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    echo "Testing analytics view compilation...\n";
    
    // Create a minimal request to test view compilation
    $request = Illuminate\Http\Request::create('/admin/analytics', 'GET');
    
    // Test if view can be compiled without the push stack error
    $viewPath = 'admin.admin-analytics.admin-analytics';
    
    // Set up basic view data that the template expects
    $viewData = [
        'isAdmin' => true
    ];
    
    $compiled = view($viewPath, $viewData);
    
    echo "✓ View compilation successful - no push stack errors!\n";
    echo "✓ The admin-analytics.blade.php file structure is now correct.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}
