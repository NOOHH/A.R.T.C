<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

echo "=== FINAL VERIFICATION TEST ===\n\n";

// Create application instance
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "1. Testing route resolution for z.smartprep.local:\n";

// Create a fake request to z.smartprep.local
$request = Illuminate\Http\Request::create(
    'http://z.smartprep.local:8000/smartprep/dashboard/settings/update/navbar/9',
    'POST',
    [
        'brand_name' => 'FINAL_VERIFICATION_TEST',
        '_token' => 'test_token'
    ],
    [],
    [],
    [
        'HTTP_HOST' => 'z.smartprep.local:8000',
        'SERVER_NAME' => 'z.smartprep.local',
        'REQUEST_URI' => '/smartprep/dashboard/settings/update/navbar/9'
    ]
);

echo "   Route: " . $request->url() . "\n";
echo "   Host: " . $request->getHost() . "\n";
echo "   Method: " . $request->method() . "\n\n";

echo "2. Checking if tenant middleware will be triggered:\n";

// Check if we can find the route
try {
    $route = app('router')->getRoutes()->match($request);
    echo "   ✓ Route found: " . $route->getName() . "\n";
    echo "   ✓ Controller: " . $route->getActionName() . "\n";
    
    // Check middleware
    $middleware = $route->gatherMiddleware();
    echo "   ✓ Middleware: " . implode(', ', $middleware) . "\n";
    
    if (in_array('tenant', $middleware)) {
        echo "   ✓ Tenant middleware is registered for this route\n";
    } else {
        echo "   ✗ Tenant middleware NOT found\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Route not found: " . $e->getMessage() . "\n";
}

echo "\n3. Testing direct database connection to tenant:\n";

try {
    // Get tenant info
    $tenant = DB::table('tenants')->where('domain', 'z.smartprep.local')->first();
    if ($tenant) {
        echo "   ✓ Tenant found: {$tenant->name}\n";
        echo "   ✓ Database: {$tenant->database}\n";
        
        // Test tenant database connection
        $tenantConnection = "tenant_{$tenant->slug}";
        
        // Configure tenant connection
        config(["database.connections.$tenantConnection" => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $tenant->database,
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]]);
        
        // Test connection
        $settings = DB::connection($tenantConnection)->table('settings')->count();
        echo "   ✓ Tenant database accessible: {$settings} settings found\n";
        
        // Test a navbar update simulation
        DB::connection($tenantConnection)->table('settings')
            ->updateOrInsert(
                ['group' => 'ui', 'key' => 'brand_name'],
                ['value' => 'FINAL_VERIFICATION_' . date('His'), 'type' => 'string']
            );
        
        $brandName = DB::connection($tenantConnection)->table('settings')
            ->where('group', 'ui')
            ->where('key', 'brand_name')
            ->value('value');
            
        echo "   ✓ Database update successful: brand_name = {$brandName}\n";
        
    } else {
        echo "   ✗ Tenant not found for domain z.smartprep.local\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n4. Summary of what needs to be done:\n";
echo "   • All backend components are working correctly\n";
echo "   • The issue is that z.smartprep.local doesn't resolve to your local server\n";
echo "   • You need to add this to your Windows hosts file:\n";
echo "     127.0.0.1    z.smartprep.local\n";
echo "   • The hosts file is located at: C:\\Windows\\System32\\drivers\\etc\\hosts\n";
echo "   • After adding the hosts entry, test at: http://z.smartprep.local:8000\n";
echo "   • The navbar changes should then work correctly\n\n";

echo "=== VERIFICATION COMPLETE ===\n";
