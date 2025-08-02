<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Create Laravel app
$app = new Application(realpath(__DIR__));

// Bootstrap the application
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Boot the application
$kernel->bootstrap();

echo "=== Module Archive Test ===\n";

// Test 1: Check if module 80 exists
echo "\n1. Checking if module 80 exists...\n";
$module = DB::table('modules')->where('modules_id', 80)->first();
if ($module) {
    echo "✓ Module 80 exists\n";
    echo "  - Name: {$module->module_name}\n";
    echo "  - Currently archived: " . ($module->is_archived ? 'Yes' : 'No') . "\n";
} else {
    echo "✗ Module 80 does not exist\n";
    exit(1);
}

// Test 2: Check route exists
echo "\n2. Checking route registration...\n";
$routes = app('router')->getRoutes();
$archiveRoute = null;
foreach ($routes as $route) {
    if ($route->uri() === 'admin/modules/{id}/archive' && in_array('POST', $route->methods())) {
        $archiveRoute = $route;
        break;
    }
}

if ($archiveRoute) {
    echo "✓ Route 'POST admin/modules/{id}/archive' is registered\n";
    echo "  - Controller: " . $archiveRoute->getActionName() . "\n";
} else {
    echo "✗ Route not found\n";
}

// Test 3: Test controller method directly
echo "\n3. Testing controller method directly...\n";
try {
    $controller = new App\Http\Controllers\AdminModuleController();
    
    // Mock a request with CSRF token
    $request = Request::create('/admin/modules/80/archive', 'POST');
    $request->headers->set('X-CSRF-TOKEN', 'test-token');
    $request->headers->set('Accept', 'application/json');
    
    // Set the request in the app
    app()->instance('request', $request);
    
    $response = $controller->archive(80);
    $data = $response->getData(true);
    
    echo "✓ Controller method executed\n";
    echo "  - Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    echo "  - Message: " . $data['message'] . "\n";
    
    // Check if module was actually archived
    $updatedModule = DB::table('modules')->where('modules_id', 80)->first();
    echo "  - Module archived in DB: " . ($updatedModule->is_archived ? 'Yes' : 'No') . "\n";
    
} catch (Exception $e) {
    echo "✗ Controller test failed: " . $e->getMessage() . "\n";
}

// Test 4: Check Laravel logs for any errors
echo "\n4. Checking recent Laravel logs...\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -20);
    
    $hasErrors = false;
    foreach ($recentLines as $line) {
        if (stripos($line, 'error') !== false || stripos($line, 'exception') !== false) {
            echo "! Recent error: " . trim($line) . "\n";
            $hasErrors = true;
        }
    }
    
    if (!$hasErrors) {
        echo "✓ No recent errors in Laravel logs\n";
    }
} else {
    echo "! Laravel log file not found\n";
}

// Test 5: Check middleware
echo "\n5. Checking middleware requirements...\n";
if ($archiveRoute) {
    $middleware = $archiveRoute->middleware();
    echo "Route middleware: " . implode(', ', $middleware) . "\n";
    
    // Check if auth middleware is applied
    if (in_array('auth', $middleware)) {
        echo "✓ Auth middleware is applied\n";
    } else {
        echo "! Auth middleware may not be applied\n";
    }
}

echo "\n=== Test Complete ===\n";
