<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Multi-Tenant Database Connections...\n\n";

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

echo "\nMulti-tenant test completed!\n";
