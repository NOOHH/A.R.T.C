<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Database Tables ===\n";
try {
    $tables = DB::select('SHOW TABLES');
    echo "Found " . count($tables) . " tables:\n";
    foreach($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- $tableName\n";
    }
    
    echo "\n=== Checking Key Tables ===\n";
    
    // Check if specific tables exist
    $keyTables = ['batches', 'users', 'referrals', 'admin_settings'];
    foreach($keyTables as $tableName) {
        try {
            $count = DB::table($tableName)->count();
            echo "✅ $tableName: $count records\n";
        } catch (Exception $e) {
            echo "❌ $tableName: MISSING or ERROR - " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
