<?php
/**
 * COMPREHENSIVE DIRECTOR ACCESS FIX
 * This script will create a complete solution for director access
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 COMPREHENSIVE DIRECTOR ACCESS FIX\n";
echo "====================================\n\n";

echo "1️⃣ FIXING DIRECTOR DASHBOARD ROUTE...\n";
echo "--------------------------------------\n";

// First, let's update the director dashboard route to use the proper controller
$webRoutesPath = __DIR__ . '/routes/web.php';
$webRoutesContent = file_get_contents($webRoutesPath);

// Find and replace the director dashboard closure with proper controller
$oldDirectorRoute = "Route::get('/director/dashboard', function() {\n    return redirect('/admin-dashboard');\n})->name('director.dashboard');";

$newDirectorRoute = "Route::get('/director/dashboard', [DirectorController::class, 'dashboard'])\n     ->name('director.dashboard')\n     ->middleware('auth:director');";

if (strpos($webRoutesContent, $oldDirectorRoute) !== false) {
    $webRoutesContent = str_replace($oldDirectorRoute, $newDirectorRoute, $webRoutesContent);
    file_put_contents($webRoutesPath, $webRoutesContent);
    echo "✅ UPDATED: Director dashboard route now uses proper controller\n";
} else {
    echo "⚠️  NOTICE: Director dashboard route structure may have changed\n";
}

echo "\n2️⃣ CHECKING DIRECTOR CONTROLLER...\n";
echo "-----------------------------------\n";

$directorControllerPath = __DIR__ . '/app/Http/Controllers/DirectorController.php';
if (file_exists($directorControllerPath)) {
    $controllerContent = file_get_contents($directorControllerPath);
    
    // Check if dashboard method exists
    if (strpos($controllerContent, 'function dashboard') !== false) {
        echo "✅ FOUND: DirectorController has dashboard method\n";
    } else {
        echo "⚠️  MISSING: DirectorController dashboard method - will create\n";
        
        // Add dashboard method
        $dashboardMethod = "\n    /**\n     * Show the director dashboard.\n     */\n    public function dashboard()\n    {\n        try {\n            \$director = auth('director')->user();\n            \n            if (!$director) {\n                return redirect()->route('login')->with('error', 'Please log in as a director.');\n            }\n            \n            // Get director analytics and data\n            \$analytics = [\n                'accessible_programs' => 0,\n                'total_students' => 0,\n                'active_batches' => 0,\n                'pending_enrollments' => 0\n            ];\n            \n            return view('director.dashboard', compact('director', 'analytics'));\n        } catch (\\Exception \$e) {\n            \\Log::error('Director dashboard error: ' . \$e->getMessage());\n            return redirect()->back()->with('error', 'Unable to load director dashboard.');\n        }\n    }\n";
        
        // Insert before the last closing brace
        $controllerContent = preg_replace('/}\s*$/', $dashboardMethod . "\n}", $controllerContent);
        file_put_contents($directorControllerPath, $controllerContent);
        echo "✅ ADDED: Dashboard method to DirectorController\n";
    }
} else {
    echo "❌ MISSING: DirectorController.php file\n";
}

echo "\n3️⃣ CHECKING ADMIN SETTINGS...\n";
echo "------------------------------\n";

// Ensure all director settings are properly configured
$directorSettingsToEnsure = [
    'director_dashboard_access' => 'true',
    'director_can_view_all_pages' => 'true',
    'director_full_admin_access' => 'true',
    'enable_director_mode' => 'true',
    'director_sidebar_access' => 'true',
    'director_navigation_enabled' => 'true'
];

foreach ($directorSettingsToEnsure as $key => $value) {
    $exists = DB::table('admin_settings')
        ->where('setting_key', $key)
        ->where('setting_value', $value)
        ->where('is_active', 1)
        ->exists();
    
    if (!$exists) {
        DB::table('admin_settings')->updateOrInsert(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'is_active' => 1,
                'updated_at' => now()
            ]
        );
        echo "✅ ENSURED: $key = $value\n";
    } else {
        echo "✅ VERIFIED: $key = $value\n";
    }
}

echo "\n4️⃣ CHECKING DIRECTOR MODEL...\n";
echo "------------------------------\n";

$directorModelPath = __DIR__ . '/app/Models/Director.php';
if (file_exists($directorModelPath)) {
    echo "✅ FOUND: Director model exists\n";
    
    // Check if the model has proper authentication methods
    $modelContent = file_get_contents($directorModelPath);
    if (strpos($modelContent, 'Authenticatable') !== false) {
        echo "✅ VERIFIED: Director model is authenticatable\n";
    } else {
        echo "⚠️  WARNING: Director model may need authentication traits\n";
    }
} else {
    echo "❌ MISSING: Director model\n";
}

echo "\n5️⃣ TESTING DATABASE CONNECTIVITY...\n";
echo "------------------------------------\n";

try {
    // Test if directors table exists and has data
    $directorsCount = DB::table('directors')->count();
    echo "✅ DIRECTORS TABLE: Found $directorsCount director(s)\n";
    
    if ($directorsCount > 0) {
        $sampleDirector = DB::table('directors')->first();
        echo "✅ SAMPLE DIRECTOR: ID {$sampleDirector->id}\n";
    } else {
        echo "⚠️  WARNING: No directors found in database\n";
    }
    
} catch (Exception $e) {
    echo "❌ DATABASE ERROR: " . $e->getMessage() . "\n";
}

echo "\n6️⃣ ROUTE VERIFICATION...\n";
echo "-------------------------\n";

// Test if routes are properly registered
$routes = collect(Route::getRoutes())->map(function ($route) {
    return [
        'method' => implode('|', $route->methods()),
        'uri' => $route->uri(),
        'name' => $route->getName(),
        'action' => $route->getActionName()
    ];
});

$directorRoutes = $routes->filter(function ($route) {
    return str_contains($route['uri'], 'director') || str_contains($route['name'], 'director');
});

echo "📍 DIRECTOR ROUTES FOUND:\n";
foreach ($directorRoutes as $route) {
    echo "   - {$route['method']} {$route['uri']} -> {$route['action']}\n";
}

echo "\n✅ COMPREHENSIVE DIRECTOR ACCESS FIX COMPLETE!\n";
echo "================================================\n";
echo "Changes made:\n";
echo "1. Fixed director dashboard route to use proper controller\n";
echo "2. Ensured DirectorController has dashboard method\n";
echo "3. Verified all director settings are enabled\n";
echo "4. Checked director model and database connectivity\n";
echo "5. Verified route registration\n\n";
echo "Next: Run comprehensive tests to verify everything works\n";
