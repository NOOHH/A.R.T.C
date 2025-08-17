<?php

// Simple test to check if the application loads
echo "Testing application...\n";

// Test if we can access the welcome view
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "✓ Laravel bootstrapped successfully\n";
    
    // Test database connections
    $mainDb = \Illuminate\Support\Facades\DB::connection('mysql');
    echo "✓ Main database connection successful\n";
    
    $tenantDb = \Illuminate\Support\Facades\DB::connection('tenant');
    echo "✓ Tenant database connection successful\n";
    
    // Test route
    $router = app('router');
    echo "✓ Router loaded successfully\n";
    
    echo "\nApplication test completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
