<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\Tenant;
use App\Services\TenantService;

echo "=== Comprehensive Tenant Controller Test ===\n\n";

// Test 1: Route Configuration
echo "1. Testing Route Configuration:\n";
$programsRoute = Route::getRoutes()->getByName('tenant.draft.admin.programs');
$packagesRoute = Route::getRoutes()->getByName('tenant.draft.admin.packages');

if ($programsRoute) {
    $action = $programsRoute->getAction();
    echo "✅ Programs route: " . $programsRoute->uri() . " -> " . $action['controller'] . "\n";
} else {
    echo "❌ Programs route not found\n";
}

if ($packagesRoute) {
    $action = $packagesRoute->getAction();
    echo "✅ Packages route: " . $packagesRoute->uri() . " -> " . $action['controller'] . "\n";
} else {
    echo "❌ Packages route not found\n";
}

echo "\n";

// Test 2: Tenant Database Connection
echo "2. Testing Tenant Database Connection:\n";
try {
    $tenant = Tenant::where('slug', 'test2')->first();
    if ($tenant) {
        echo "✅ Tenant 'test2' found in main database\n";
        echo "   Database: " . $tenant->database_name . "\n";
        
        // Test tenant database connection
        $tenantService = new TenantService();
        $tenantService->switchToTenant($tenant);
        
        $programsCount = DB::table('programs')->count();
        echo "✅ Tenant database connected - Programs count: $programsCount\n";
        
        // Switch back to main
        $tenantService->switchToMain();
        echo "✅ Switched back to main database\n";
    } else {
        echo "❌ Tenant 'test2' not found in main database\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Database Isolation
echo "3. Testing Database Isolation:\n";
try {
    // Check main database programs
    $mainPrograms = DB::table('programs')->count();
    echo "Main database programs: $mainPrograms\n";
    
    // Check tenant database programs
    $tenantService = new TenantService();
    $tenant = Tenant::where('slug', 'test2')->first();
    $tenantService->switchToTenant($tenant);
    
    $tenantPrograms = DB::table('programs')->count();
    echo "Tenant database programs: $tenantPrograms\n";
    
    if ($mainPrograms !== $tenantPrograms) {
        echo "✅ Database isolation confirmed - different program counts\n";
    } else {
        echo "⚠️  Same program count - may indicate shared data\n";
    }
    
    // Switch back to main
    $tenantService->switchToMain();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Controller Method Testing
echo "4. Testing Controller Methods:\n";
try {
    // Test TenantAdminProgramController index method
    $controller = new \App\Http\Controllers\Tenant\TenantAdminProgramController(new TenantService());
    
    // Mock request
    $request = new \Illuminate\Http\Request();
    $request->merge(['tenant' => 'test2']);
    
    // Test index method
    $response = $controller->index('test2');
    if ($response instanceof \Illuminate\View\View) {
        echo "✅ TenantAdminProgramController::index() works correctly\n";
        $data = $response->getData();
        echo "   Programs in view: " . count($data['programs']) . "\n";
    } else {
        echo "❌ TenantAdminProgramController::index() returned unexpected response\n";
    }
    
} catch (Exception $e) {
    echo "❌ Controller test error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Program Creation Test
echo "5. Testing Program Creation in Tenant Database:\n";
try {
    $tenantService = new TenantService();
    $tenant = Tenant::where('slug', 'test2')->first();
    $tenantService->switchToTenant($tenant);
    
    // Count before
    $beforeCount = DB::table('programs')->count();
    
    // Create test program
    $testProgram = [
        'program_name' => 'Test Tenant Program ' . time(),
        'program_description' => 'This is a test program created in tenant database',
        'created_by_admin_id' => 1,
        'is_archived' => false,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    DB::table('programs')->insert($testProgram);
    
    // Count after
    $afterCount = DB::table('programs')->count();
    
    if ($afterCount > $beforeCount) {
        echo "✅ Program created successfully in tenant database\n";
        echo "   Before: $beforeCount, After: $afterCount\n";
    } else {
        echo "❌ Program creation failed\n";
    }
    
    // Switch back to main
    $tenantService->switchToMain();
    
    // Verify it's not in main database
    $mainCount = DB::table('programs')->count();
    echo "Main database programs: $mainCount (should be unchanged)\n";
    
} catch (Exception $e) {
    echo "❌ Program creation test error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Route URL Generation
echo "6. Testing Route URL Generation:\n";
try {
    $programsUrl = route('tenant.draft.admin.programs', ['tenant' => 'test2']);
    $packagesUrl = route('tenant.draft.admin.packages', ['tenant' => 'test2']);
    
    echo "Programs URL: $programsUrl\n";
    echo "Packages URL: $packagesUrl\n";
    
    if (strpos($programsUrl, '/draft/test2/admin/programs') !== false) {
        echo "✅ Programs URL generated correctly\n";
    } else {
        echo "❌ Programs URL incorrect\n";
    }
    
    if (strpos($packagesUrl, '/draft/test2/admin/packages') !== false) {
        echo "✅ Packages URL generated correctly\n";
    } else {
        echo "❌ Packages URL incorrect\n";
    }
    
} catch (Exception $e) {
    echo "❌ URL generation error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
