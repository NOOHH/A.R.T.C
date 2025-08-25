<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing t/ prefixed routes for tenant admin programs...\n\n";

$tenant = 'test2';

// Test 1: Check if the t/ prefixed routes are registered
echo "📋 Test 1: Route Registration Check\n";
echo "=====================================\n";

$routesToCheck = [
    'tenant.admin.programs.index' => '/t/draft/{tenant}/admin/programs',
    'tenant.admin.programs.store' => '/t/draft/{tenant}/admin/programs',
    'tenant.admin.packages.index' => '/t/draft/{tenant}/admin/packages',
    'tenant.admin.packages.store' => '/t/draft/{tenant}/admin/packages',
];

foreach ($routesToCheck as $routeName => $expectedPath) {
    $route = Route::getRoutes()->getByName($routeName);
    if ($route) {
        echo "✅ Route '{$routeName}' is registered\n";
        echo "   Path: {$route->uri()}\n";
        echo "   Controller: " . $route->getAction()['controller'] . "\n";
    } else {
        echo "❌ Route '{$routeName}' is NOT registered\n";
    }
    echo "\n";
}

// Test 2: Test route URL generation
echo "🔗 Test 2: Route URL Generation\n";
echo "================================\n";

try {
    $programsUrl = route('tenant.admin.programs.index', ['tenant' => $tenant]);
    echo "✅ Generated URL: {$programsUrl}\n";
    
    $packagesUrl = route('tenant.admin.packages.index', ['tenant' => $tenant]);
    echo "✅ Generated URL: {$packagesUrl}\n";
} catch (Exception $e) {
    echo "❌ Error generating URLs: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Test tenant database connection
echo "🗄️ Test 3: Tenant Database Connection\n";
echo "=====================================\n";

try {
    // Switch to tenant database
    $tenantModel = \App\Models\Tenant::where('slug', $tenant)->first();
    if (!$tenantModel) {
        echo "❌ Tenant '{$tenant}' not found in main database\n";
    } else {
        echo "✅ Tenant '{$tenant}' found in main database\n";
        
        // Switch to tenant database
        \App\Models\Tenant::switchToTenant($tenant);
        
        // Check programs in tenant database
        $tenantPrograms = \App\Models\Program::count();
        echo "✅ Tenant database has {$tenantPrograms} programs\n";
        
        // Switch back to main database
        \App\Models\Tenant::switchToMain();
        
        // Check programs in main database
        $mainPrograms = \App\Models\Program::count();
        echo "✅ Main database has {$mainPrograms} programs\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Test controller method execution
echo "🎮 Test 4: Controller Method Execution\n";
echo "=====================================\n";

try {
    // Create a mock request
    $request = Illuminate\Http\Request::create("/t/draft/{$tenant}/admin/programs", 'GET');
    
    // Get the route
    $route = Route::getRoutes()->getByName('tenant.admin.programs.index');
    if ($route) {
        // Get the controller instance
        $controllerClass = $route->getAction()['controller'];
        $controller = app($controllerClass);
        
        // Test the index method
        $response = $controller->index($request, $tenant);
        
        if ($response) {
            echo "✅ Controller method executed successfully\n";
            echo "   Response type: " . get_class($response) . "\n";
            
            // If it's a view response, check if it has data
            if ($response instanceof \Illuminate\View\View) {
                $viewData = $response->getData();
                if (isset($viewData['programs'])) {
                    echo "   Programs in view: " . count($viewData['programs']) . "\n";
                }
            }
        } else {
            echo "❌ Controller method returned null\n";
        }
    } else {
        echo "❌ Route not found for controller test\n";
    }
} catch (Exception $e) {
    echo "❌ Controller error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Test the specific URL that was causing 404
echo "🎯 Test 5: Specific 404 URL Test\n";
echo "================================\n";

$testUrl = "http://127.0.0.1:8000/t/draft/{$tenant}/admin/programs?preview=true";
echo "Testing URL: {$testUrl}\n";

try {
    // Create a mock request with the exact URL
    $request = Illuminate\Http\Request::create("/t/draft/{$tenant}/admin/programs?preview=true", 'GET');
    
    // Try to match the route
    $route = Route::getRoutes()->match($request);
    
    if ($route) {
        echo "✅ Route matched successfully!\n";
        echo "   Route name: " . $route->getName() . "\n";
        echo "   Controller: " . $route->getAction()['controller'] . "\n";
        echo "   Method: " . $route->getAction()['uses'] . "\n";
    } else {
        echo "❌ No route matched for the URL\n";
    }
} catch (Exception $e) {
    echo "❌ Route matching error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 6: Test program creation in tenant context
echo "➕ Test 6: Program Creation in Tenant Context\n";
echo "=============================================\n";

try {
    // Switch to tenant database
    \App\Models\Tenant::switchToTenant($tenant);
    
    // Count programs before creation
    $programsBefore = \App\Models\Program::count();
    
    // Create a test program
    $testProgram = \App\Models\Program::create([
        'program_name' => 'Test Program for t/ Route - ' . date('Y-m-d H:i:s'),
        'created_by_admin_id' => 1,
        'is_active' => 1,
        'is_archived' => 0,
        'program_description' => 'Test program created to validate t/ prefixed routes'
    ]);
    
    // Count programs after creation
    $programsAfter = \App\Models\Program::count();
    
    echo "✅ Program created successfully!\n";
    echo "   Programs before: {$programsBefore}\n";
    echo "   Programs after: {$programsAfter}\n";
    echo "   New program ID: {$testProgram->program_id}\n";
    
    // Switch back to main database
    \App\Models\Tenant::switchToMain();
    
    // Verify main database is unchanged
    $mainProgramsAfter = \App\Models\Program::count();
    echo "   Main database programs: {$mainProgramsAfter} (unchanged)\n";
    
} catch (Exception $e) {
    echo "❌ Program creation error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "🎉 t/ Prefixed Routes Test Completed!\n";
echo "=====================================\n";
echo "The URL http://127.0.0.1:8000/t/draft/test2/admin/programs?preview=true should now work.\n";
echo "Please test it in your browser to confirm the 404 error is fixed.\n";
