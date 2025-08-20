<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Client;
use App\Models\Tenant;
use App\Models\Setting;
use App\Services\TenantService;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING z.smartprep.local SETTINGS INTEGRATION ===\n\n";

try {
    // 1. Find the tenant for z.smartprep.local
    echo "1. Looking up tenant for z.smartprep.local...\n";
    
    $tenant = Tenant::where('domain', 'z.smartprep.local')
                   ->orWhere('slug', 'z')
                   ->first();
    
    if (!$tenant) {
        echo "❌ Tenant not found for z.smartprep.local\n";
        echo "Available tenants:\n";
        $tenants = Tenant::all(['id', 'slug', 'domain', 'database_name']);
        foreach ($tenants as $t) {
            echo "  - ID: {$t->id}, Slug: {$t->slug}, Domain: {$t->domain}, DB: {$t->database_name}\n";
        }
        exit(1);
    }
    
    echo "✅ Tenant found: {$tenant->slug} (DB: {$tenant->database_name})\n";
    echo "Domains: {$tenant->domain}\n\n";
    
    // 2. Test tenant database connection and settings
    echo "2. Testing tenant database settings...\n";
    $tenantService = app(TenantService::class);
    $tenantService->switchToTenant($tenant);
    
    // Get navbar settings
    $navbarSettings = Setting::getGroup('navbar');
    echo "Current navbar settings in tenant DB:\n";
    echo "  - brand_name: " . ($navbarSettings['brand_name'] ?? 'NOT_SET') . "\n";
    echo "  - show_login_button: " . ($navbarSettings['show_login_button'] ?? 'NOT_SET') . "\n";
    echo "  - style: " . ($navbarSettings['style'] ?? 'NOT_SET') . "\n";
    echo "  - brand_logo: " . ($navbarSettings['brand_logo'] ?? 'NOT_SET') . "\n\n";
    
    // 3. Update a test value that should be visible
    echo "3. Setting a distinctive test value...\n";
    $testValue = "TENANT_TEST_" . date('His');
    Setting::set('navbar', 'brand_name', $testValue);
    echo "✅ Set brand_name to: {$testValue}\n\n";
    
    // 4. Switch back to main
    $tenantService->switchToMain();
    
    // 5. Check tenant domain configuration
    echo "4. Checking tenant domain configuration...\n";
    
    // Check if there's a route or configuration for z.smartprep.local
    $routesContent = file_get_contents(__DIR__ . '/routes/web.php');
    if (strpos($routesContent, 'smartprep.local') !== false) {
        echo "✅ Found smartprep.local references in routes\n";
    } else {
        echo "⚠️  No smartprep.local references in main routes\n";
    }
    
    // 6. Check if there's tenant middleware or domain routing
    echo "\n5. Checking for tenant middleware...\n";
    $middlewareFiles = glob(__DIR__ . '/app/Http/Middleware/*Tenant*.php');
    if (!empty($middlewareFiles)) {
        echo "✅ Found tenant middleware files:\n";
        foreach ($middlewareFiles as $file) {
            echo "  - " . basename($file) . "\n";
        }
    } else {
        echo "⚠️  No tenant middleware found\n";
    }
    
    // 7. Check TenantService implementation
    echo "\n6. Checking TenantService configuration...\n";
    $tenantServiceFile = __DIR__ . '/app/Services/TenantService.php';
    if (file_exists($tenantServiceFile)) {
        $content = file_get_contents($tenantServiceFile);
        if (strpos($content, 'switchToTenant') !== false) {
            echo "✅ TenantService has switchToTenant method\n";
        }
        if (strpos($content, 'config') !== false) {
            echo "✅ TenantService modifies database config\n";
        }
    }
    
    echo "\n=== TENANT CONFIGURATION CHECK ===\n";
    echo "Tenant database: {$tenant->database_name}\n";
    echo "Test brand name: {$testValue}\n";
    echo "\nTo verify z.smartprep.local reads tenant settings:\n";
    echo "1. Visit: http://z.smartprep.local\n";
    echo "2. Check if brand name shows: {$testValue}\n";
    echo "3. If not, check:\n";
    echo "   - DNS/hosts file points z.smartprep.local to 127.0.0.1\n";
    echo "   - Apache/nginx vhost configuration\n";
    echo "   - Tenant middleware is active for that domain\n";
    echo "   - Application correctly switches to tenant DB on domain match\n";
    
    // 8. Check if we can find tenant resolution logic
    echo "\n7. Looking for tenant resolution logic...\n";
    $kernelFile = __DIR__ . '/app/Http/Kernel.php';
    if (file_exists($kernelFile)) {
        $kernelContent = file_get_contents($kernelFile);
        if (strpos($kernelContent, 'tenant') !== false || strpos($kernelContent, 'Tenant') !== false) {
            echo "✅ Found tenant references in HTTP Kernel\n";
        } else {
            echo "⚠️  No tenant middleware registered in HTTP Kernel\n";
        }
    }
    
    // Check for tenant route files
    $tenantRoutes = glob(__DIR__ . '/routes/*tenant*.php');
    if (!empty($tenantRoutes)) {
        echo "✅ Found tenant route files:\n";
        foreach ($tenantRoutes as $route) {
            echo "  - " . basename($route) . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    try {
        app(TenantService::class)->switchToMain();
    } catch (\Exception $switchError) {
        echo "❌ Failed to switch back to main database\n";
    }
}
