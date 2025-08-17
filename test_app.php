<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Multi-Tenant Application...\n\n";

// Test main database connection
try {
    echo "Testing main database (smartprep)...\n";
    $result = DB::connection('mysql')->select('SELECT 1 as test');
    echo "✓ Main database connection successful\n";
} catch (Exception $e) {
    echo "✗ Main database connection failed: " . $e->getMessage() . "\n";
}

// Test tenant database connection
try {
    echo "Testing tenant database (smartprep_artc)...\n";
    $result = DB::connection('tenant')->select('SELECT 1 as test');
    echo "✓ Tenant database connection successful\n";
} catch (Exception $e) {
    echo "✗ Tenant database connection failed: " . $e->getMessage() . "\n";
}

// Test route resolution
try {
    echo "\nTesting route resolution...\n";
    $router = app('router');
    $routes = $router->getRoutes();
    
    $homeRoute = null;
    foreach ($routes as $route) {
        if ($route->uri() === '/') {
            $homeRoute = $route;
            break;
        }
    }
    
    if ($homeRoute) {
        echo "✓ Root route (/) found\n";
        echo "  Controller: " . $homeRoute->getActionName() . "\n";
    } else {
        echo "✗ Root route (/) not found\n";
    }
} catch (Exception $e) {
    echo "✗ Route testing failed: " . $e->getMessage() . "\n";
}

echo "\nMulti-tenant test completed!\n";
