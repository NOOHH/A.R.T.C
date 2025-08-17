<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== CHECKING DATABASE ISSUE ===\n\n";
    
    // Check current database connection
    $currentDB = config('database.default');
    $currentDBName = config("database.connections.{$currentDB}.database");
    echo "Current default connection: {$currentDB}\n";
    echo "Current database: {$currentDBName}\n\n";
    
    // Check which tables exist in main database
    echo "1. Tables in main database (smartprep):\n";
    $mainTables = DB::connection('mysql')->select('SHOW TABLES');
    $mainTableNames = array_map(function($table) { return array_values((array)$table)[0]; }, $mainTables);
    
    $keyTables = ['admins', 'directors', 'professors', 'users'];
    foreach($keyTables as $table) {
        if (in_array($table, $mainTableNames)) {
            echo "   ✅ {$table} exists\n";
        } else {
            echo "   ❌ {$table} missing\n";
        }
    }
    
    // Check which tables exist in tenant database
    echo "\n2. Tables in tenant database (smartprep_artc):\n";
    $tenantTables = DB::connection()->select('SHOW TABLES FROM smartprep_artc');
    $tenantTableNames = array_map(function($table) { return array_values((array)$table)[0]; }, $tenantTables);
    
    foreach($keyTables as $table) {
        if (in_array($table, $tenantTableNames)) {
            echo "   ✅ {$table} exists\n";
        } else {
            echo "   ❌ {$table} missing\n";
        }
    }
    
    // Check if middleware is working during login
    echo "\n3. Testing middleware behavior:\n";
    
    // Simulate localhost request (like during login)
    $domain = '127.0.0.1';
    echo "   Domain: {$domain}\n";
    
    // Check tenant resolution
    if (in_array($domain, ['localhost', '127.0.0.1', 'artc.test'])) {
        $resolvedDomain = 'artc.smartprep.local';
        echo "   Resolved to: {$resolvedDomain}\n";
        
        $tenant = DB::connection('mysql')->table('tenants')->where('domain', $resolvedDomain)->first();
        if ($tenant) {
            echo "   ✅ Tenant found: {$tenant->name}\n";
            echo "   Tenant database: {$tenant->database_name}\n";
            
            // Test switching to tenant database
            config(['database.connections.tenant.database' => $tenant->database_name]);
            DB::purge('tenant');
            
            // Check if directors table exists in tenant database
            $tenantTables = DB::connection('tenant')->select('SHOW TABLES');
            $tenantTableNames = array_map(function($table) { return array_values((array)$table)[0]; }, $tenantTables);
            
            if (in_array('directors', $tenantTableNames)) {
                echo "   ✅ Directors table exists in tenant database\n";
                
                // Check if admin@artc.com exists in tenant directors table
                $director = DB::connection('tenant')->table('directors')->where('directors_email', 'admin@artc.com')->first();
                if ($director) {
                    echo "   ✅ admin@artc.com found in tenant directors table\n";
                } else {
                    echo "   ❌ admin@artc.com NOT found in tenant directors table\n";
                }
            } else {
                echo "   ❌ Directors table does NOT exist in tenant database\n";
            }
        } else {
            echo "   ❌ Tenant not found\n";
        }
    }
    
    // Check the current connection after potential switching
    echo "\n4. Current connection status:\n";
    echo "   Default connection: " . config('database.default') . "\n";
    echo "   Active database: " . DB::connection()->getDatabaseName() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
