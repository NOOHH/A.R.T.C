<?php

echo "🔧 COMPREHENSIVE MODULE ARCHIVE DEBUG\n";
echo "=====================================\n\n";

// Start Laravel properly
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "1. CHECKING ROUTE REGISTRATION:\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $foundRoute = false;
    
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'admin/modules') && str_contains($route->uri(), 'archive')) {
            echo "✅ Found route: " . $route->methods()[0] . " " . $route->uri() . " -> " . $route->getActionName() . "\n";
            $foundRoute = true;
        }
    }
    
    if (!$foundRoute) {
        echo "❌ No module archive routes found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error checking routes: " . $e->getMessage() . "\n";
}

echo "\n2. CHECKING DATABASE STRUCTURE:\n";
try {
    $moduleColumns = \Illuminate\Support\Facades\DB::select("DESCRIBE modules");
    foreach ($moduleColumns as $column) {
        if ($column->Field === 'is_archived') {
            echo "✅ Found is_archived column: {$column->Field} ({$column->Type}, Default: {$column->Default})\n";
        }
    }
    
    // Check sample module
    $module = \Illuminate\Support\Facades\DB::table('modules')->where('modules_id', 80)->first();
    if ($module) {
        echo "✅ Module 80 exists: {$module->module_name}, is_archived: " . ($module->is_archived ? 'true' : 'false') . "\n";
    } else {
        echo "❌ Module 80 not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error checking database: " . $e->getMessage() . "\n";
}

echo "\n3. CHECKING CONTROLLER METHOD:\n";
try {
    $controller = new \App\Http\Controllers\AdminModuleController();
    $reflection = new ReflectionClass($controller);
    
    if ($reflection->hasMethod('archive')) {
        $method = $reflection->getMethod('archive');
        echo "✅ AdminModuleController::archive() method exists\n";
        echo "   - Method is public: " . ($method->isPublic() ? 'Yes' : 'No') . "\n";
        echo "   - Parameters: " . $method->getNumberOfParameters() . "\n";
    } else {
        echo "❌ AdminModuleController::archive() method not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error checking controller: " . $e->getMessage() . "\n";
}

echo "\n4. CHECKING MIDDLEWARE:\n";
try {
    // Find the archive route and check its middleware
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'admin/modules/{id}/archive')) {
            $middleware = $route->middleware();
            echo "✅ Archive route middleware: " . implode(', ', $middleware) . "\n";
            break;
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error checking middleware: " . $e->getMessage() . "\n";
}

echo "\n🏁 DEBUG COMPLETE\n";
