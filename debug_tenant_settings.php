<?php
/**
 * Debug Tenant Navbar Settings
 */

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DEBUG TENANT NAVBAR SETTINGS\n";
echo "================================\n\n";

// Check tenant 15 (Test1) settings
$websiteId = 15;

try {
    $client = \App\Models\Client::find($websiteId);
    if ($client) {
        echo "✅ Found client: {$client->slug}\n";
        
        $tenantObj = \App\Models\Tenant::where('slug', $client->slug)->first();
        if ($tenantObj) {
            echo "✅ Found tenant: {$tenantObj->slug}\n";
            
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToTenant($tenantObj);
            
            // Check navbar settings
            $navbarSettings = \App\Models\Setting::getGroup('navbar');
            echo "\n📱 NAVBAR SETTINGS:\n";
            if ($navbarSettings) {
                echo "  Brand Name: " . $navbarSettings->get('brand_name', 'NOT SET') . "\n";
                echo "  Brand Logo: " . $navbarSettings->get('brand_logo', 'NOT SET') . "\n";
                echo "  Full navbar settings: " . json_encode($navbarSettings->toArray(), JSON_PRETTY_PRINT) . "\n";
            } else {
                echo "  ❌ No navbar settings found\n";
            }
            
            // Check admin panel settings
            $adminSettings = \App\Models\Setting::getGroup('admin_panel');
            echo "\n🔧 ADMIN PANEL SETTINGS:\n";
            if ($adminSettings) {
                echo "  Brand Name: " . $adminSettings->get('brand_name', 'NOT SET') . "\n";
                echo "  Brand Logo: " . $adminSettings->get('brand_logo', 'NOT SET') . "\n";
                echo "  Full admin settings: " . json_encode($adminSettings->toArray(), JSON_PRETTY_PRINT) . "\n";
            } else {
                echo "  ❌ No admin panel settings found\n";
            }
            
            // Check if there are other settings
            $allSettings = \App\Models\Setting::all();
            echo "\n📋 ALL SETTINGS:\n";
            foreach ($allSettings as $setting) {
                if (strpos(strtolower($setting->key), 'brand') !== false || 
                    strpos(strtolower($setting->key), 'name') !== false ||
                    strpos(strtolower($setting->key), 'navbar') !== false) {
                    echo "  {$setting->key}: {$setting->value}\n";
                }
            }
            
            $tenantService->switchToMain();
        } else {
            echo "❌ Tenant not found for client slug: {$client->slug}\n";
        }
    } else {
        echo "❌ Client not found for ID: {$websiteId}\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
