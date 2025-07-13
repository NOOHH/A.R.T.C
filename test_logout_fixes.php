<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing logout functionality fixes:\n\n";

try {
    // Test 1: Check if routes are registered
    echo "1. Route Registration Test:\n";
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $logoutRoutes = [];
    
    foreach ($routes as $route) {
        if (strpos($route->uri, 'logout') !== false) {
            $logoutRoutes[] = [
                'method' => implode('|', $route->methods),
                'uri' => $route->uri,
                'name' => $route->getName(),
                'action' => $route->getActionName()
            ];
        }
    }
    
    foreach ($logoutRoutes as $route) {
        echo "   ✓ {$route['method']} /{$route['uri']} -> {$route['name']} ({$route['action']})\n";
    }
    
    // Test 2: Check if controller method exists
    echo "\n2. Controller Method Test:\n";
    $controller = new \App\Http\Controllers\UnifiedLoginController();
    if (method_exists($controller, 'logout')) {
        echo "   ✓ UnifiedLoginController::logout method exists\n";
    } else {
        echo "   ❌ UnifiedLoginController::logout method NOT found\n";
    }
    
    // Test 3: Check if views exist
    echo "\n3. View Files Test:\n";
    $views = [
        'homepage' => 'resources/views/homepage.blade.php',
        'logout-test' => 'resources/views/logout-test.blade.php'
    ];
    
    foreach ($views as $name => $path) {
        if (file_exists($path)) {
            echo "   ✓ {$name} view exists: {$path}\n";
        } else {
            echo "   ❌ {$name} view NOT found: {$path}\n";
        }
    }
    
    // Test 4: Test CSRF token generation
    echo "\n4. CSRF Token Test:\n";
    session_start();
    $token = csrf_token();
    echo "   ✓ CSRF token generated: " . substr($token, 0, 10) . "...\n";
    
    // Test 5: Check middleware registration
    echo "\n5. Middleware Registration Test:\n";
    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
    $middleware = $kernel->getMiddleware();
    
    $csrfFound = false;
    foreach ($middleware as $mw) {
        if (strpos($mw, 'VerifyCsrfToken') !== false) {
            $csrfFound = true;
            break;
        }
    }
    
    echo "   " . ($csrfFound ? "✓" : "❌") . " CSRF middleware registered\n";
    
    echo "\n✅ All tests completed!\n";
    echo "\nNow you can test the logout functionality by visiting:\n";
    echo "• Homepage: http://127.0.0.1:8001/\n";
    echo "• Logout Test: http://127.0.0.1:8001/logout-test\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
