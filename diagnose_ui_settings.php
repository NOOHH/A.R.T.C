<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== DEBUGGING UI_SETTINGS ERROR ===\n\n";
    
    // Check current default connection
    $currentDB = config('database.default');
    $currentDBName = config("database.connections.{$currentDB}.database");
    echo "Current connection: {$currentDB}\n";
    echo "Current database: {$currentDBName}\n\n";
    
    // Check if ui_settings table exists
    $tables = DB::select("SHOW TABLES LIKE 'ui_settings'");
    
    if (empty($tables)) {
        echo "❌ ui_settings table does NOT exist in {$currentDBName}\n";
        
        // Check in smartprep_artc database
        echo "\nChecking smartprep_artc database...\n";
        $artcTables = DB::select("SHOW TABLES FROM smartprep_artc LIKE 'ui_settings'");
        
        if (!empty($artcTables)) {
            echo "✅ ui_settings table EXISTS in smartprep_artc\n";
            $columns = DB::select("DESCRIBE smartprep_artc.ui_settings");
            echo "Columns in smartprep_artc.ui_settings:\n";
            foreach($columns as $column) {
                echo "- {$column->Field} ({$column->Type})\n";
            }
        } else {
            echo "❌ ui_settings table does NOT exist in smartprep_artc either\n";
        }
        
    } else {
        echo "✅ ui_settings table exists in {$currentDBName}\n";
        $columns = DB::select("DESCRIBE ui_settings");
        echo "Columns:\n";
        foreach($columns as $column) {
            echo "- {$column->Field} ({$column->Type})\n";
        }
    }
    
    echo "\n=== CHECKING TENANT MIDDLEWARE ===\n";
    
    // Simulate what happens when accessing localhost
    $domain = '127.0.0.1';
    echo "Domain being processed: {$domain}\n";
    
    // Check tenant resolution
    if (in_array($domain, ['localhost', '127.0.0.1', 'artc.test'])) {
        $resolvedDomain = 'artc.smartprep.local';
        echo "Resolved to: {$resolvedDomain}\n";
        
        $tenant = DB::connection('mysql')->table('tenants')->where('domain', $resolvedDomain)->first();
        if ($tenant) {
            echo "✅ Tenant found: {$tenant->name}\n";
            echo "Database: {$tenant->database_name}\n";
            
            // Check if tenant database has ui_settings
            $tenantTables = DB::select("SHOW TABLES FROM `{$tenant->database_name}` LIKE 'ui_settings'");
            if (!empty($tenantTables)) {
                echo "✅ ui_settings exists in tenant database\n";
                $tenantColumns = DB::select("DESCRIBE `{$tenant->database_name}`.ui_settings");
                echo "Tenant ui_settings columns:\n";
                foreach($tenantColumns as $column) {
                    echo "- {$column->Field} ({$column->Type})\n";
                }
            } else {
                echo "❌ ui_settings does NOT exist in tenant database: {$tenant->database_name}\n";
            }
        } else {
            echo "❌ No tenant found for domain: {$resolvedDomain}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
