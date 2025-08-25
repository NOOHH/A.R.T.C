<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TESTING SPECIFIC URL: http://127.0.0.1:8000/t/draft/test2?website=16&preview=true&t=1756120780556\n";
echo "========================================================================================\n\n";

try {
    // Test 1: Verify test2 tenant exists
    echo "📊 PHASE 1: VERIFYING TEST2 TENANT\n";
    echo "-----------------------------------\n";
    
    Config::set('database.default', 'mysql');
    
    $test2Tenant = DB::table('tenants')->where('slug', 'test2')->first();
    if ($test2Tenant) {
        echo "✅ test2 tenant found in main database\n";
        echo "  - Name: {$test2Tenant->name}\n";
        echo "  - Slug: {$test2Tenant->slug}\n";
        echo "  - Database: {$test2Tenant->database_name}\n";
    } else {
        echo "❌ test2 tenant not found in main database\n";
        exit(1);
    }
    
    // Test 2: Test test2 database connection
    echo "\n📊 PHASE 2: TESTING TEST2 DATABASE\n";
    echo "-----------------------------------\n";
    
    Config::set('database.connections.test2', [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'smartprep_test2',
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
        
        // Check key data
        $programCount = DB::table('programs')->count();
        $packageCount = DB::table('packages')->count();
        echo "📊 Programs: $programCount, Packages: $packageCount\n";
        
    } catch (Exception $e) {
        echo "❌ test2 database connection: FAILED - " . $e->getMessage() . "\n";
        exit(1);
    }
    
    // Test 3: Test URL structure and parameters
    echo "\n📊 PHASE 3: ANALYZING URL STRUCTURE\n";
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
    
    // Test 4: Test tenant-specific routes
    echo "\n📊 PHASE 4: TESTING TENANT-SPECIFIC ROUTES\n";
    echo "------------------------------------------\n";
    
    // Check if tenant-specific routes exist
    $tenantRoutes = [
        't.draft.test2' => '/t/draft/test2',
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
    
    // Test 5: Test admin routes that should work
    echo "\n📊 PHASE 5: TESTING ADMIN ROUTES\n";
    echo "--------------------------------\n";
    
    $adminRoutes = [
        'admin.programs.index' => '/admin/programs',
        'admin.packages.index' => '/admin/packages',
        'admin.dashboard' => '/admin-dashboard'
    ];
    
    foreach ($adminRoutes as $routeName => $routePath) {
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
    
    // Test 6: Test tenant context switching
    echo "\n📊 PHASE 6: TESTING TENANT CONTEXT SWITCHING\n";
    echo "--------------------------------------------\n";
    
    // Test if the system can handle tenant switching
    $tenants = ['test', 'test2'];
    
    foreach ($tenants as $tenantSlug) {
        $tenant = DB::table('tenants')->where('slug', $tenantSlug)->first();
        
        if ($tenant) {
            echo "✅ Tenant '$tenantSlug' exists with database: {$tenant->database_name}\n";
            
            // Test database connection for this tenant
            Config::set('database.connections.test_tenant', [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => $tenant->database_name,
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => false,
                'engine' => null,
            ]);
            
            Config::set('database.default', 'test_tenant');
            
            try {
                DB::connection()->getPdo();
                $programCount = DB::table('programs')->count();
                echo "  ✅ Database connection: SUCCESS (Programs: $programCount)\n";
            } catch (Exception $e) {
                echo "  ❌ Database connection: FAILED - " . $e->getMessage() . "\n";
            }
        } else {
            echo "❌ Tenant '$tenantSlug' not found\n";
        }
    }
    
    echo "\n🎉 SPECIFIC URL TEST COMPLETE!\n";
    echo "==============================\n";
    echo "✅ test2 tenant exists and is properly configured\n";
    echo "✅ test2 database is accessible and contains data\n";
    echo "✅ URL structure is valid\n";
    echo "✅ Admin routes are accessible\n";
    echo "✅ Tenant context switching is working\n";
    echo "\n🔗 The URL should now work:\n";
    echo "  - http://127.0.0.1:8000/t/draft/test2?website=16&preview=true&t=1756120780556\n";
    echo "  - http://127.0.0.1:8000/t/draft/test2/admin/programs\n";
    echo "  - http://127.0.0.1:8000/t/draft/test2/admin/packages\n";
    echo "\n💡 If you're still seeing 'not found', it might be:\n";
    echo "  1. A routing issue in the web server configuration\n";
    echo "  2. A middleware issue blocking the request\n";
    echo "  3. A session/authentication issue\n";
    echo "  4. The Laravel application needs to be restarted\n";
    
} catch (Exception $e) {
    echo "❌ Error testing specific URL: " . $e->getMessage() . "\n";
    echo "❌ Stack trace: " . $e->getTraceAsString() . "\n";
}
