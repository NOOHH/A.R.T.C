<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Test website=15 parameter handling
    $website = 15;
    $client = \App\Models\Client::find($website);
    
    if ($client) {
        echo 'Client found: ' . $client->name . ' (slug: ' . $client->slug . ')' . PHP_EOL;
        
        // Find tenant by slug
        $tenant = \App\Models\Tenant::where('slug', $client->slug)->first();
        if ($tenant) {
            echo 'Tenant found: ' . $tenant->name . ' (database: ' . $tenant->database_name . ')' . PHP_EOL;
            
            // Test switching to tenant database and loading settings
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToTenant($tenant);
            
            // Get navbar settings
            $navbarSettings = \App\Models\Setting::getGroup('navbar');
            if ($navbarSettings) {
                $brandName = $navbarSettings->get('brand_name', 'Default Brand');
                echo 'Brand name from tenant settings: ' . $brandName . PHP_EOL;
            } else {
                echo 'No navbar settings found in tenant database' . PHP_EOL;
            }
            
            $tenantService->switchToMain();
            
        } else {
            echo 'No tenant found for client slug: ' . $client->slug . PHP_EOL;
        }
    } else {
        echo 'No client found for website=15' . PHP_EOL;
    }
    
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
