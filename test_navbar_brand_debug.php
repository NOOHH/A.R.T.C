<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== NAVBAR BRAND NAME DEBUG TEST ===\n\n";

try {
    echo "1. Testing tenant database direct access:\n";
    
    // Connect to tenant database directly
    config(['database.connections.tenant.database' => 'smartprep_artc']);
    DB::purge('tenant');
    config(['database.default' => 'tenant']);
    DB::purge('mysql');
    
    $currentDb = DB::select('SELECT DATABASE() as db')[0]->db;
    echo "   Current database: $currentDb\n";
    
    echo "2. Testing navbar settings query:\n";
    
    // Test direct query to ui_settings for navbar group
    $navbarSettings = DB::table('ui_settings')->where('section', 'navbar')->get();
    echo "   Found " . $navbarSettings->count() . " navbar ui_settings records\n";
    if ($navbarSettings->count() > 0) {
        $first = $navbarSettings->first();
        echo "   Columns available: " . implode(', ', array_keys((array)$first)) . "\n";
        foreach ($navbarSettings as $setting) {
            // Check what columns exist
            $settingArray = (array)$setting;
            if (isset($settingArray['setting_key']) && isset($settingArray['setting_value'])) {
                echo "   - {$settingArray['setting_key']}: {$settingArray['setting_value']}\n";
            } elseif (isset($settingArray['key']) && isset($settingArray['value'])) {
                echo "   - {$settingArray['key']}: {$settingArray['value']}\n";
            } else {
                echo "   - Record: " . json_encode($settingArray) . "\n";
            }
        }
    }
    
    echo "3. Testing Setting::getGroup method:\n";
    $navbarGroup = \App\Models\Setting::getGroup('navbar');
    echo "   Navbar group type: " . gettype($navbarGroup) . "\n";
    if ($navbarGroup) {
        echo "   Navbar group data:\n";
        foreach ($navbarGroup as $key => $value) {
            echo "   - $key: $value\n";
        }
    }
    
    echo "4. Testing specific brand_name value:\n";
    $brandName = \App\Models\Setting::get('navbar', 'brand_name', 'DEFAULT');
    echo "   Brand name: '$brandName'\n";
    
    echo "5. Testing AdminPreviewCustomization trait simulation:\n";
    
    // Simulate what happens in the trait
    $client = \App\Models\Client::first();
    if ($client) {
        echo "   Using client: {$client->slug}\n";
        
        $tenant = \App\Models\Tenant::where('slug', $client->slug)->first();
        if ($tenant) {
            echo "   Using tenant: {$tenant->database_name}\n";
            
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToTenant($tenant);
            
            $navbarSettings = \App\Models\Setting::getGroup('navbar');
            $settings = [
                'navbar' => [
                    'brand_name' => $navbarSettings ? $navbarSettings->get('brand_name', 'Ascendo Review and Training Center') : 'Ascendo Review and Training Center',
                    'brand_logo' => $navbarSettings ? $navbarSettings->get('brand_logo', null) : null,
                ],
            ];
            
            echo "   Final settings:\n";
            echo "   - brand_name: '{$settings['navbar']['brand_name']}'\n";
            echo "   - brand_logo: '{$settings['navbar']['brand_logo']}'\n";
            
            $tenantService->switchToMain();
        }
    }
    
    echo "6. Testing admin-dashboard route response for brand name:\n";
    
    // Use file_get_contents to test the route
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'header' => 'User-Agent: Brand Test Script'
        ]
    ]);
    
    $response = @file_get_contents('http://127.0.0.1:8000/t/draft/artc/admin-dashboard?website=1', false, $context);
    if ($response) {
        // Look for brand text in response
        if (preg_match('/<span class="brand-text[^>]*>(.*?)<\/span>/', $response, $matches)) {
            echo "   Current brand text in response: '{$matches[1]}'\n";
        } else {
            echo "   Could not find brand text in response\n";
        }
        
        // Look for ARTC
        if (strpos($response, 'ARTC') !== false) {
            echo "   ❌ Response still contains 'ARTC'\n";
        } else {
            echo "   ✅ Response does not contain 'ARTC'\n";
        }
        
        // Look for Admin Portal
        if (strpos($response, 'Admin Portal') !== false) {
            echo "   ❌ Response still contains 'Admin Portal'\n";
        } else {
            echo "   ✅ Response does not contain 'Admin Portal'\n";
        }
        
    } else {
        echo "   ERROR: Could not fetch admin dashboard response\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
