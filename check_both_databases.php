<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DATABASE CONNECTIVITY TEST ===\n\n";

// Test mysql connection (smartprep database)
echo "1. Testing mysql connection (smartprep database):\n";
try {
    $db = DB::connection('mysql')->select('select database() as db');
    echo "   Connected to: " . $db[0]->db . "\n";
    
    // Check table structure first
    $columns = DB::connection('mysql')->select("SHOW COLUMNS FROM admins");
    echo "   Columns in admins table:\n";
    foreach($columns as $col) {
        echo "     - " . $col->Field . " (" . $col->Type . ")\n";
    }
    
    $admins = DB::connection('mysql')->table('admins')->get();
    echo "   Found " . $admins->count() . " admins\n";
} catch(Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test tenant connection (smartprep_artc database)
echo "2. Testing tenant connection (smartprep_artc database):\n";
try {
    $db = DB::connection('tenant')->select('select database() as db');
    echo "   Connected to: " . $db[0]->db . "\n";
    
    // Check table structure first
    $columns = DB::connection('tenant')->select("SHOW COLUMNS FROM admins");
    echo "   Columns in admins table:\n";
    foreach($columns as $col) {
        echo "     - " . $col->Field . " (" . $col->Type . ")\n";
    }
    
    $admins = DB::connection('tenant')->table('admins')->get();
    echo "   Found " . $admins->count() . " admins\n";
} catch(Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
