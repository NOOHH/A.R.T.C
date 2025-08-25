<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 CHECKING TENANT DATABASE STRUCTURE\n";
echo "====================================\n\n";

try {
    // Check what tables exist in the tenant database
    $tenantTables = DB::connection('tenant')->select('SHOW TABLES');
    echo "📋 Tables in tenant database (smartprep_artc):\n";
    
    $tableNames = [];
    foreach ($tenantTables as $table) {
        $tableName = array_values((array) $table)[0];
        $tableNames[] = $tableName;
        echo "  ✅ $tableName\n";
    }
    
    echo "\n🔍 Checking for specific tables mentioned in error:\n";
    
    $criticalTables = ['plans', 'form_requirements', 'packages', 'programs', 'students', 'users'];
    
    foreach ($criticalTables as $table) {
        if (in_array($table, $tableNames)) {
            echo "  ✅ $table - EXISTS\n";
        } else {
            echo "  ❌ $table - MISSING\n";
            
            // Check if it exists in main database
            try {
                $mainTables = DB::connection()->select('SHOW TABLES');
                $mainTableNames = [];
                foreach ($mainTables as $mainTable) {
                    $mainTableNames[] = array_values((array) $mainTable)[0];
                }
                
                if (in_array($table, $mainTableNames)) {
                    echo "      💡 $table exists in MAIN database - might need to use main DB for this table\n";
                }
            } catch (Exception $e) {
                echo "      ⚠️  Could not check main database\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error checking tenant database: " . $e->getMessage() . "\n";
    
    // Try to check main database instead
    echo "\n🔍 Checking main database as fallback:\n";
    try {
        $mainTables = DB::connection()->select('SHOW TABLES');
        echo "📋 Tables in main database (smartprep):\n";
        
        foreach ($mainTables as $table) {
            $tableName = array_values((array) $table)[0];
            echo "  ✅ $tableName\n";
        }
    } catch (Exception $mainE) {
        echo "❌ Error checking main database too: " . $mainE->getMessage() . "\n";
    }
}

echo "\n💡 RECOMMENDATION:\n";
echo "==================\n";
echo "Some tables like 'plans' and 'form_requirements' might be shared across all tenants\n";
echo "and should remain in the main database. We may need to use a mixed approach:\n";
echo "• Tenant-specific data (students, enrollments, registrations) → tenant database\n";
echo "• Shared configuration (plans, form_requirements) → main database\n";

echo "\n=== TENANT DATABASE ANALYSIS COMPLETE ===\n";
?>
