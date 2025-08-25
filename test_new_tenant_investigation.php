<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Tenant;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 COMPREHENSIVE NEW TENANT INVESTIGATION\n";
echo "==========================================\n\n";

// Test 1: Check main database for tenants
echo "📊 PHASE 1: CHECKING MAIN DATABASE FOR TENANTS\n";
echo "-----------------------------------------------\n";

try {
    Config::set('database.default', 'mysql');
    
    // Check if tenants table exists
    $tenantsTableExists = DB::getSchemaBuilder()->hasTable('tenants');
    echo "📋 Tenants table exists in main database: " . ($tenantsTableExists ? 'YES' : 'NO') . "\n";
    
    if ($tenantsTableExists) {
        $tenants = DB::table('tenants')->get();
        echo "🏢 Found " . count($tenants) . " tenants in main database:\n";
        
        foreach ($tenants as $tenant) {
            echo "  - {$tenant->name} (Slug: {$tenant->slug}, DB: {$tenant->database_name})\n";
        }
        
        // Look specifically for test2
        $test2Tenant = DB::table('tenants')->where('slug', 'test2')->first();
        if ($test2Tenant) {
            echo "✅ Found test2 tenant: {$test2Tenant->name} (DB: {$test2Tenant->database_name})\n";
        } else {
            echo "❌ test2 tenant NOT found in main database\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error checking main database: " . $e->getMessage() . "\n";
}

// Test 2: Check if test2 database exists
echo "\n📊 PHASE 2: CHECKING TEST2 DATABASE\n";
echo "------------------------------------\n";

try {
    // Try to connect to test2 database
    $test2DbName = 'smartprep_test2';
    Config::set('database.connections.test2', [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => $test2DbName,
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => null,
    ]);
    
    Config::set('database.default', 'test2');
    
    try {
        DB::connection()->getPdo();
        echo "✅ test2 database connection: SUCCESS\n";
        
        // Check what tables exist
        $tables = DB::select('SHOW TABLES');
        echo "📋 Tables in test2 database:\n";
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            echo "  - $tableName\n";
        }
        
        // Check key tables
        $keyTables = ['programs', 'packages', 'modules', 'courses'];
        foreach ($keyTables as $table) {
            $exists = DB::getSchemaBuilder()->hasTable($table);
            echo "📊 Table '$table' exists: " . ($exists ? 'YES' : 'NO') . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ test2 database connection: FAILED - " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error setting up test2 database connection: " . $e->getMessage() . "\n";
}

// Test 3: Check routes for test2 tenant
echo "\n📊 PHASE 3: CHECKING ROUTES FOR TEST2 TENANT\n";
echo "---------------------------------------------\n";

try {
    // Check if tenant-specific routes exist
    $tenantRoutes = [
        't.draft.test2.admin.programs' => '/t/draft/test2/admin/programs',
        't.draft.test2.admin.packages' => '/t/draft/test2/admin/packages',
        't.draft.test2.admin.dashboard' => '/t/draft/test2/admin-dashboard'
    ];
    
    foreach ($tenantRoutes as $routeName => $routePath) {
        try {
            $route = Route::getRoutes()->getByName($routeName);
            if ($route) {
                echo "✅ Route '$routeName' exists and points to: " . $route->uri() . "\n";
            } else {
                echo "❌ Route '$routeName' not found\n";
            }
        } catch (Exception $e) {
            echo "❌ Error checking route '$routeName': " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error checking routes: " . $e->getMessage() . "\n";
}

// Test 4: Check tenant middleware and context
echo "\n📊 PHASE 4: CHECKING TENANT CONTEXT SYSTEM\n";
echo "-------------------------------------------\n";

try {
    // Check if tenant context helper exists
    if (class_exists('App\Helpers\TenantContextHelper')) {
        echo "✅ TenantContextHelper class exists\n";
        
        // Test tenant context
        $tenantContext = new \App\Helpers\TenantContextHelper();
        echo "✅ TenantContextHelper instantiated successfully\n";
        
    } else {
        echo "❌ TenantContextHelper class not found\n";
    }
    
    // Check if tenant service exists
    if (class_exists('App\Services\TenantService')) {
        echo "✅ TenantService class exists\n";
    } else {
        echo "❌ TenantService class not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error checking tenant context: " . $e->getMessage() . "\n";
}

// Test 5: Check URL structure and parameters
echo "\n📊 PHASE 5: ANALYZING URL STRUCTURE\n";
echo "-----------------------------------\n";

$testUrl = "http://127.0.0.1:8000/t/draft/test2?website=16&preview=true&t=1756120780556";
echo "🔗 Test URL: $testUrl\n";

// Parse URL components
$urlParts = parse_url($testUrl);
echo "📋 URL Components:\n";
echo "  - Path: " . ($urlParts['path'] ?? 'N/A') . "\n";
echo "  - Query: " . ($urlParts['query'] ?? 'N/A') . "\n";

// Parse query parameters
if (isset($urlParts['query'])) {
    parse_str($urlParts['query'], $queryParams);
    echo "📋 Query Parameters:\n";
    foreach ($queryParams as $key => $value) {
        echo "  - $key: $value\n";
    }
}

// Test 6: Check if test2 tenant needs to be created
echo "\n📊 PHASE 6: TENANT CREATION ANALYSIS\n";
echo "------------------------------------\n";

try {
    Config::set('database.default', 'mysql');
    
    // Check if we need to create test2 tenant
    $test2Exists = DB::table('tenants')->where('slug', 'test2')->exists();
    
    if (!$test2Exists) {
        echo "⚠️ test2 tenant does not exist in main database\n";
        echo "🔧 Need to create test2 tenant\n";
        
        // Check what tenants exist for reference
        $existingTenants = DB::table('tenants')->take(3)->get();
        echo "📋 Sample existing tenants:\n";
        foreach ($existingTenants as $tenant) {
            echo "  - {$tenant->name} (Slug: {$tenant->slug}, DB: {$tenant->database_name})\n";
        }
        
    } else {
        echo "✅ test2 tenant exists in main database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error checking tenant existence: " . $e->getMessage() . "\n";
}

echo "\n=== NEW TENANT INVESTIGATION COMPLETE ===\n";
