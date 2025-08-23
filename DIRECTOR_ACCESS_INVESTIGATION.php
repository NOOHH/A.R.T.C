<?php
/**
 * COMPREHENSIVE DIRECTOR ACCESS INVESTIGATION
 * This script will thoroughly examine all aspects of director access
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

echo "🔍 COMPREHENSIVE DIRECTOR ACCESS INVESTIGATION\n";
echo "==============================================\n\n";

// 1. Database Investigation
echo "1️⃣ DATABASE INVESTIGATION:\n";
echo "----------------------------\n";

try {
    // Check admin_settings table structure and data
    echo "📊 Admin Settings Table:\n";
    $adminSettings = DB::table('admin_settings')->get();
    foreach ($adminSettings as $setting) {
        echo "   - {$setting->setting_key}: {$setting->setting_value} (active: {$setting->is_active})\n";
    }
    
    echo "\n📊 Admins Table (checking for directors):\n";
    $admins = DB::table('admins')->select('id', 'name', 'email', 'role')->get();
    foreach ($admins as $admin) {
        echo "   - ID: {$admin->id}, Name: {$admin->name}, Role: {$admin->role}, Email: {$admin->email}\n";
    }
    
    echo "\n📊 Users Table (checking for directors):\n";
    $users = DB::table('users')->select('id', 'name', 'email', 'role')->get();
    foreach ($users as $user) {
        echo "   - ID: {$user->id}, Name: {$user->name}, Role: {$user->role}, Email: {$user->email}\n";
    }
    
} catch (Exception $e) {
    echo "❌ DATABASE ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Routes Investigation
echo "2️⃣ ROUTES INVESTIGATION:\n";
echo "-------------------------\n";

try {
    // Get all routes and filter for director/admin related ones
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName()
        ];
    });
    
    echo "📍 Admin Dashboard Routes:\n";
    $adminRoutes = $routes->filter(function ($route) {
        return str_contains($route['uri'], 'admin') && str_contains($route['uri'], 'dashboard');
    });
    
    foreach ($adminRoutes as $route) {
        echo "   - {$route['method']} {$route['uri']} -> {$route['action']}\n";
    }
    
    echo "\n📍 Director Specific Routes:\n";
    $directorRoutes = $routes->filter(function ($route) {
        return str_contains($route['uri'], 'director') || str_contains($route['action'], 'Director');
    });
    
    foreach ($directorRoutes as $route) {
        echo "   - {$route['method']} {$route['uri']} -> {$route['action']}\n";
    }
    
    if ($directorRoutes->isEmpty()) {
        echo "   ⚠️  NO SPECIFIC DIRECTOR ROUTES FOUND\n";
    }
    
} catch (Exception $e) {
    echo "❌ ROUTES ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Controller Investigation
echo "3️⃣ CONTROLLER INVESTIGATION:\n";
echo "------------------------------\n";

$controllerPaths = [
    'app/Http/Controllers/AdminController.php',
    'app/Http/Controllers/Admin/AdminController.php',
    'app/Http/Controllers/DirectorController.php',
    'app/Http/Controllers/Admin/DirectorController.php'
];

foreach ($controllerPaths as $path) {
    if (file_exists($path)) {
        echo "✅ FOUND: $path\n";
        
        // Check for director-related methods
        $content = file_get_contents($path);
        if (str_contains($content, 'director')) {
            echo "   📍 Contains director-related code\n";
        }
    } else {
        echo "❌ MISSING: $path\n";
    }
}

echo "\n";

// 4. Middleware Investigation
echo "4️⃣ MIDDLEWARE INVESTIGATION:\n";
echo "-----------------------------\n";

$middlewarePaths = [
    'app/Http/Middleware/DirectorMiddleware.php',
    'app/Http/Middleware/AdminMiddleware.php',
    'app/Http/Middleware/CheckDirectorAccess.php'
];

foreach ($middlewarePaths as $path) {
    if (file_exists($path)) {
        echo "✅ FOUND: $path\n";
    } else {
        echo "❌ MISSING: $path\n";
    }
}

echo "\n";

// 5. View Investigation
echo "5️⃣ VIEW INVESTIGATION:\n";
echo "-----------------------\n";

$viewPaths = [
    'resources/views/admin/dashboard.blade.php',
    'resources/views/director/dashboard.blade.php',
    'resources/views/admin/layouts/sidebar.blade.php',
    'resources/views/layouts/admin-sidebar.blade.php'
];

foreach ($viewPaths as $path) {
    if (file_exists($path)) {
        echo "✅ FOUND: $path\n";
    } else {
        echo "❌ MISSING: $path\n";
    }
}

echo "\n";

// 6. Configuration Investigation
echo "6️⃣ CONFIGURATION INVESTIGATION:\n";
echo "---------------------------------\n";

try {
    // Check auth configuration
    $authConfig = config('auth');
    echo "📍 Auth Guards:\n";
    foreach ($authConfig['guards'] as $guard => $config) {
        echo "   - $guard: driver={$config['driver']}, provider={$config['provider']}\n";
    }
    
    echo "\n📍 Auth Providers:\n";
    foreach ($authConfig['providers'] as $provider => $config) {
        echo "   - $provider: driver={$config['driver']}, model={$config['model']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ CONFIG ERROR: " . $e->getMessage() . "\n";
}

echo "\n🔍 INVESTIGATION COMPLETE!\n";
echo "========================\n";
echo "Next steps:\n";
echo "1. Analyze the findings above\n";
echo "2. Create appropriate routes if missing\n";
echo "3. Implement director controller if needed\n";
echo "4. Set up proper middleware and permissions\n";
echo "5. Create comprehensive tests\n";
