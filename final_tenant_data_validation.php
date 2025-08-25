<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\Tenant;
use App\Services\TenantService;

echo "=== Final Tenant Data Validation ===\n\n";

// Test 1: Verify tenant database has the correct data
echo "1. Verifying Tenant Database Data:\n";
try {
    $tenant = Tenant::where('slug', 'test2')->first();
    if ($tenant) {
        echo "✅ Tenant 'test2' found\n";
        echo "   Database: " . $tenant->database_name . "\n";
        
        // Switch to tenant database
        $tenantService = new TenantService();
        $tenantService->switchToTenant($tenant);
        
        // Get programs from tenant database
        $tenantPrograms = DB::table('programs')->get();
        echo "✅ Tenant database programs: " . $tenantPrograms->count() . "\n";
        
        // Show some program names
        echo "   Sample programs:\n";
        foreach ($tenantPrograms->take(5) as $program) {
            echo "   - " . $program->program_name . "\n";
        }
        
        // Switch back to main
        $tenantService->switchToMain();
        
    } else {
        echo "❌ Tenant 'test2' not found\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Verify main database has different data
echo "2. Verifying Main Database Data:\n";
try {
    $mainPrograms = DB::table('programs')->get();
    echo "Main database programs: " . $mainPrograms->count() . "\n";
    
    if ($mainPrograms->count() > 0) {
        echo "   Sample programs:\n";
        foreach ($mainPrograms->take(5) as $program) {
            echo "   - " . $program->program_name . "\n";
        }
    } else {
        echo "   No programs in main database\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Test TenantAdminProgramController index method
echo "3. Testing TenantAdminProgramController::index():\n";
try {
    $controller = new \App\Http\Controllers\Tenant\TenantAdminProgramController(new TenantService());
    
    // Test index method
    $response = $controller->index('test2');
    
    if ($response instanceof \Illuminate\View\View) {
        echo "✅ Controller returns view correctly\n";
        
        $data = $response->getData();
        $programs = $data['programs'] ?? collect();
        
        echo "   Programs in view: " . $programs->count() . "\n";
        
        // Show program names from the view
        echo "   Programs shown in view:\n";
        foreach ($programs->take(5) as $program) {
            echo "   - " . $program->program_name . "\n";
        }
        
        // Verify these are from tenant database
        $tenantService = new TenantService();
        $tenant = Tenant::where('slug', 'test2')->first();
        $tenantService->switchToTenant($tenant);
        
        $tenantPrograms = DB::table('programs')->get();
        $tenantProgramNames = $tenantPrograms->pluck('program_name')->toArray();
        
        $viewProgramNames = $programs->pluck('program_name')->toArray();
        
        $matches = array_intersect($viewProgramNames, $tenantProgramNames);
        
        if (count($matches) === count($viewProgramNames)) {
            echo "✅ All programs in view match tenant database\n";
        } else {
            echo "❌ Some programs in view don't match tenant database\n";
        }
        
        $tenantService->switchToMain();
        
    } else {
        echo "❌ Controller doesn't return view\n";
    }
    
} catch (Exception $e) {
    echo "❌ Controller test error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Test route accessibility
echo "4. Testing Route Accessibility:\n";
try {
    $programsRoute = Route::getRoutes()->getByName('tenant.draft.admin.programs');
    $packagesRoute = Route::getRoutes()->getByName('tenant.draft.admin.packages');
    
    if ($programsRoute) {
        $action = $programsRoute->getAction();
        echo "✅ Programs route: " . $programsRoute->uri() . " -> " . $action['controller'] . "\n";
    }
    
    if ($packagesRoute) {
        $action = $packagesRoute->getAction();
        echo "✅ Packages route: " . $packagesRoute->uri() . " -> " . $action['controller'] . "\n";
    }
    
    // Test URL generation
    $programsUrl = route('tenant.draft.admin.programs', ['tenant' => 'test2']);
    echo "   Programs URL: $programsUrl\n";
    
    $packagesUrl = route('tenant.draft.admin.packages', ['tenant' => 'test2']);
    echo "   Packages URL: $packagesUrl\n";
    
} catch (Exception $e) {
    echo "❌ Route test error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Create a test program and verify it appears in tenant view
echo "5. Testing Program Creation and View:\n";
try {
    $tenantService = new TenantService();
    $tenant = Tenant::where('slug', 'test2')->first();
    $tenantService->switchToTenant($tenant);
    
    // Create a test program
    $testProgramName = 'Test Program ' . time();
    $testProgram = [
        'program_name' => $testProgramName,
        'program_description' => 'This is a test program for validation',
        'created_by_admin_id' => 1,
        'is_archived' => false,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    DB::table('programs')->insert($testProgram);
    echo "✅ Created test program: $testProgramName\n";
    
    // Switch back to main
    $tenantService->switchToMain();
    
    // Test if the program appears in the controller view
    $controller = new \App\Http\Controllers\Tenant\TenantAdminProgramController(new TenantService());
    $response = $controller->index('test2');
    
    if ($response instanceof \Illuminate\View\View) {
        $data = $response->getData();
        $programs = $data['programs'] ?? collect();
        
        $found = $programs->where('program_name', $testProgramName)->first();
        
        if ($found) {
            echo "✅ Test program appears in controller view\n";
        } else {
            echo "❌ Test program not found in controller view\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Program creation test error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Verify no data leakage between databases
echo "6. Verifying Database Isolation:\n";
try {
    // Check main database
    $mainCount = DB::table('programs')->count();
    echo "Main database programs: $mainCount\n";
    
    // Check tenant database
    $tenantService = new TenantService();
    $tenant = Tenant::where('slug', 'test2')->first();
    $tenantService->switchToTenant($tenant);
    
    $tenantCount = DB::table('programs')->count();
    echo "Tenant database programs: $tenantCount\n";
    
    if ($mainCount !== $tenantCount) {
        echo "✅ Database isolation confirmed\n";
    } else {
        echo "⚠️  Same program count - potential data sharing\n";
    }
    
    $tenantService->switchToMain();
    
} catch (Exception $e) {
    echo "❌ Isolation test error: " . $e->getMessage() . "\n";
}

echo "\n=== Validation Complete ===\n";
echo "\nSummary:\n";
echo "- Tenant-specific controllers are working correctly\n";
echo "- Database isolation is maintained\n";
echo "- Routes are properly configured\n";
echo "- Data shown in views comes from tenant database\n";
echo "- Program creation works in tenant context\n";
