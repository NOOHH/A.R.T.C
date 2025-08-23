<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Testing Admin Preview Customization Integration\n";
    echo "==========================================\n\n";
    
    // Simulate the admin dashboard request with website parameter
    $_GET['website'] = '15';
    $_GET['preview'] = 'true';
    $_GET['t'] = time();
    
    // Test 1: Check client lookup
    $websiteId = 15;
    $client = \App\Models\Client::find($websiteId);
    
    if ($client) {
        echo "✅ Client found: {$client->name} (slug: {$client->slug})\n";
        
        // Test 2: Check tenant lookup
        $tenant = \App\Models\Tenant::where('slug', $client->slug)->first();
        if ($tenant) {
            echo "✅ Tenant found: {$tenant->name} (database: {$tenant->database_name})\n";
            
            // Test 3: Check tenant database switching and settings
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToTenant($tenant);
            
            $navbarSettings = \App\Models\Setting::getGroup('navbar');
            if ($navbarSettings) {
                $brandName = $navbarSettings->get('brand_name', 'Default Brand');
                echo "✅ Brand name from tenant: '{$brandName}'\n";
                
                if ($brandName === 'Test1') {
                    echo "✅ CUSTOMIZATION WORKING! Brand shows 'Test1' instead of default\n";
                } else {
                    echo "❌ Customization issue: Expected 'Test1', got '{$brandName}'\n";
                }
            } else {
                echo "❌ No navbar settings found in tenant database\n";
            }
            
            $tenantService->switchToMain();
            
        } else {
            echo "❌ No tenant found for client slug: {$client->slug}\n";
        }
    } else {
        echo "❌ No client found for website=15\n";
    }
    
    echo "\n";
    echo "Test URLs:\n";
    echo "- Dashboard: http://localhost/t/draft/test1/admin-dashboard?website=15&preview=true&t=" . time() . "\n";
    echo "- Announcements: http://localhost/t/draft/test1/admin/announcements?website=15&preview=true&t=" . time() . "\n";
    
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
