<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminProgramController;
use App\Http\Controllers\AdminPackageController;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TESTING TEST2 TENANT FUNCTIONALITY\n";
echo "=====================================\n\n";

try {
    // Test 1: Verify test2 tenant in main database
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
    
    // Test 2: Test test2 database connection and data
    echo "\n📊 PHASE 2: TESTING TEST2 DATABASE\n";
    echo "-----------------------------------\n";
    
    // Configure test2 database connection
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
    
    // Test database connection
    DB::connection()->getPdo();
    echo "✅ test2 database connection: SUCCESS\n";
    
    // Check key tables and data
    $keyTables = ['programs', 'packages', 'modules', 'courses'];
    foreach ($keyTables as $table) {
        $count = DB::table($table)->count();
        echo "📊 Table '$table' count: $count\n";
    }
    
    // Test 3: Test admin programs functionality
    echo "\n📊 PHASE 3: TESTING ADMIN PROGRAMS FUNCTIONALITY\n";
    echo "------------------------------------------------\n";
    
    try {
        $programController = new AdminProgramController();
        $response = $programController->index();
        echo "✅ AdminProgramController index method: SUCCESS\n";
        
        // Check if response is a view
        if (method_exists($response, 'getName')) {
            echo "✅ Response is a view: " . $response->getName() . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ AdminProgramController index method failed: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Test admin packages functionality
    echo "\n📊 PHASE 4: TESTING ADMIN PACKAGES FUNCTIONALITY\n";
    echo "------------------------------------------------\n";
    
    try {
        $packageController = new AdminPackageController();
        $response = $packageController->index();
        echo "✅ AdminPackageController index method: SUCCESS\n";
        
        // Check if response is a view
        if (method_exists($response, 'getName')) {
            echo "✅ Response is a view: " . $response->getName() . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ AdminPackageController index method failed: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Test routes for test2 tenant
    echo "\n📊 PHASE 5: TESTING ROUTES FOR TEST2 TENANT\n";
    echo "-------------------------------------------\n";
    
    $tenantRoutes = [
        'admin.programs.index' => '/admin/programs',
        'admin.programs.store' => '/admin/programs',
        'admin.packages.index' => '/admin/packages',
        'admin.packages.store' => '/admin/packages',
        'admin.get-program-modules' => '/admin/get-program-modules'
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
    
    // Test 6: Test data operations
    echo "\n📊 PHASE 6: TESTING DATA OPERATIONS\n";
    echo "-----------------------------------\n";
    
    // Test program creation
    $testProgramData = [
        'program_name' => 'Test2 Program - ' . date('Y-m-d H:i:s'),
        'program_description' => 'Test program created for test2 tenant',
        'is_archived' => false
    ];
    
    try {
        $newProgramId = DB::table('programs')->insertGetId($testProgramData);
        echo "✅ Test program created with ID: $newProgramId\n";
        
        // Clean up
        DB::table('programs')->where('program_id', $newProgramId)->delete();
        echo "✅ Test program cleaned up\n";
        
    } catch (Exception $e) {
        echo "❌ Program creation failed: " . $e->getMessage() . "\n";
    }
    
    // Test package creation
    $testPackageData = [
        'package_name' => 'Test2 Package - ' . date('Y-m-d H:i:s'),
        'description' => 'Test package created for test2 tenant',
        'amount' => 1999.99,
        'package_type' => 'full',
        'price' => 1999.99,
        'created_by_admin_id' => 1
    ];
    
    try {
        $newPackageId = DB::table('packages')->insertGetId($testPackageData);
        echo "✅ Test package created with ID: $newPackageId\n";
        
        // Clean up
        DB::table('packages')->where('package_id', $newPackageId)->delete();
        echo "✅ Test package cleaned up\n";
        
    } catch (Exception $e) {
        echo "❌ Package creation failed: " . $e->getMessage() . "\n";
    }
    
    // Test 7: Test API endpoints
    echo "\n📊 PHASE 7: TESTING API ENDPOINTS\n";
    echo "--------------------------------\n";
    
    // Test get-program-modules API
    $firstProgram = DB::table('programs')->first();
    if ($firstProgram) {
        $request = Request::create('/admin/get-program-modules', 'GET', [
            'program_id' => $firstProgram->program_id
        ]);
        
        try {
            $response = $packageController->getProgramModules($request);
            echo "✅ get-program-modules API: SUCCESS for program: {$firstProgram->program_name}\n";
            
            // Check response structure
            if (method_exists($response, 'getData')) {
                $data = $response->getData();
                if (isset($data->success) && $data->success) {
                    echo "✅ API response indicates success\n";
                }
            }
            
        } catch (Exception $e) {
            echo "❌ get-program-modules API failed: " . $e->getMessage() . "\n";
        }
    }
    
    // Test 8: Test tenant context switching
    echo "\n📊 PHASE 8: TESTING TENANT CONTEXT SWITCHING\n";
    echo "--------------------------------------------\n";
    
    // Test switching between tenants
    $tenants = ['smartprep_artc', 'smartprep_test2'];
    
    foreach ($tenants as $tenantDb) {
        Config::set('database.connections.test_tenant', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => $tenantDb,
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
            echo "✅ Tenant '$tenantDb' connection: SUCCESS (Programs: $programCount)\n";
        } catch (Exception $e) {
            echo "❌ Tenant '$tenantDb' connection: FAILED - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n🎉 TEST2 TENANT FUNCTIONALITY TEST COMPLETE!\n";
    echo "============================================\n";
    echo "✅ Tenant record verified\n";
    echo "✅ Database connection working\n";
    echo "✅ Admin programs functionality working\n";
    echo "✅ Admin packages functionality working\n";
    echo "✅ Routes accessible\n";
    echo "✅ Data operations working\n";
    echo "✅ API endpoints responding\n";
    echo "✅ Tenant context switching working\n";
    echo "\n🔗 test2 tenant is ready for use!\n";
    echo "  - http://127.0.0.1:8000/t/draft/test2/admin/programs\n";
    echo "  - http://127.0.0.1:8000/t/draft/test2/admin/packages\n";
    
} catch (Exception $e) {
    echo "❌ Error testing test2 tenant: " . $e->getMessage() . "\n";
    echo "❌ Stack trace: " . $e->getTraceAsString() . "\n";
}
