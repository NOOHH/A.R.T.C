<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== TENANT PREVIEW ROUTING TEST ===\n\n";

// Test URLs to check
$testUrls = [
    // Dashboard
    'http://127.0.0.1:8000/t/draft/test1/student/dashboard?website=15&preview=true',
    
    // Calendar  
    'http://127.0.0.1:8000/t/draft/test1/student/calendar?website=15&preview=true',
    
    // Enrolled Courses
    'http://127.0.0.1:8000/t/draft/test1/student/enrolled-courses?website=15&preview=true',
    
    // Settings
    'http://127.0.0.1:8000/t/draft/test1/student/settings?website=15&preview=true',
    
    // Meetings
    'http://127.0.0.1:8000/t/draft/test1/student/meetings?website=15&preview=true',
];

foreach ($testUrls as $url) {
    echo "Testing: $url\n";
    
    // Parse URL to create request
    $urlParts = parse_url($url);
    $path = $urlParts['path'];
    $query = $urlParts['query'] ?? '';
    
    try {
        // Create request
        $request = Request::create($path . ($query ? '?' . $query : ''), 'GET');
        $request->headers->set('HOST', '127.0.0.1:8000');
        
        // Test route resolution
        $router = app('router');
        $route = $router->getRoutes()->match($request);
        
        echo "  ✓ Route found: " . $route->getName() . "\n";
        echo "  ✓ Action: " . $route->getActionName() . "\n";
        
        // Check middleware
        $middleware = $route->gatherMiddleware();
        echo "  ✓ Middleware: " . implode(', ', $middleware) . "\n";
        
    } catch (Exception $e) {
        echo "  ✗ Route error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== TENANT SETTINGS TEST ===\n\n";

// Test tenant switching
try {
    $tenant = \App\Models\Tenant::where('slug', 'test1')->first();
    if ($tenant) {
        echo "✓ Tenant found: {$tenant->name}\n";
        echo "  Database: {$tenant->database}\n";
        
        $tenantService = app(\App\Services\TenantService::class);
        $tenantService->switchToTenant($tenant);
        
        // Test if we can access tenant database
        $programCount = \App\Models\Program::count();
        echo "  ✓ Programs in tenant DB: {$programCount}\n";
        
        $tenantService->switchToMain();
        echo "  ✓ Switched back to main DB\n";
        
    } else {
        echo "✗ Tenant 'test1' not found\n";
        echo "Available tenants:\n";
        $tenants = \App\Models\Tenant::all();
        foreach ($tenants as $t) {
            echo "  - {$t->slug} ({$t->name})\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Tenant test error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
