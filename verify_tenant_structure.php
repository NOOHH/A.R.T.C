<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Verifying tenant database structure...\n\n";
    
    // Get tables from template database
    $templateTables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'smartprep_artc' ORDER BY TABLE_NAME");
    echo "Template database (smartprep_artc) has " . count($templateTables) . " tables:\n";
    
    // Get tables from new tenant database
    $tenantTables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'smartprep_demo-smartprep-local' ORDER BY TABLE_NAME");
    echo "New tenant database has " . count($tenantTables) . " tables:\n";
    
    // Compare structures
    $templateTableNames = array_map(function($table) { return $table->TABLE_NAME; }, $templateTables);
    $tenantTableNames = array_map(function($table) { return $table->TABLE_NAME; }, $tenantTables);
    
    $missing = array_diff($templateTableNames, $tenantTableNames);
    $extra = array_diff($tenantTableNames, $templateTableNames);
    
    if (empty($missing) && empty($extra)) {
        echo "✅ Perfect match! Both databases have the same table structure.\n\n";
        
        // Show some key tables
        echo "Key tables copied:\n";
        $keyTables = ['users', 'courses', 'quizzes', 'enrollments', 'quiz_attempts'];
        foreach ($keyTables as $table) {
            if (in_array($table, $templateTableNames)) {
                // Count records in template
                $templateCount = DB::select("SELECT COUNT(*) as count FROM `smartprep_artc`.`$table`")[0]->count;
                $tenantCount = DB::select("SELECT COUNT(*) as count FROM `smartprep_demo-smartprep-local`.`$table`")[0]->count;
                echo "- $table: Template($templateCount) -> Tenant($tenantCount)\n";
            }
        }
    } else {
        if (!empty($missing)) {
            echo "❌ Missing tables in tenant: " . implode(', ', $missing) . "\n";
        }
        if (!empty($extra)) {
            echo "❌ Extra tables in tenant: " . implode(', ', $extra) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
