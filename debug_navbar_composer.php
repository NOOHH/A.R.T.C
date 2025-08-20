<?php
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Setting;
use App\Models\Client;
use App\Models\Tenant;
use App\Services\TenantService;

echo "=== DEBUG: NavbarComposer Logic ===\n\n";

try {
    // Check client and tenant relationship
    $client = Client::find(10);
    if (!$client) {
        echo "❌ Client 10 not found\n";
        exit;
    }
    
    echo "✅ Client: {$client->name}\n";
    echo "✅ Client slug: {$client->slug}\n\n";
    
    // Find tenant by slug matching client slug
    $tenant = Tenant::where('slug', $client->slug)->first();
    if (!$tenant) {
        echo "❌ No tenant found with slug: {$client->slug}\n";
        
        // List all tenants
        $allTenants = Tenant::all();
        echo "Available tenants:\n";
        foreach ($allTenants as $t) {
            echo "  - ID: {$t->id}, Slug: {$t->slug}, Name: {$t->name}, DB: {$t->database_name}\n";
        }
        exit;
    }
    
    echo "✅ Tenant: {$tenant->slug}\n";
    echo "✅ Tenant DB: {$tenant->database_name}\n\n";
    
    // Switch to tenant database
    $tenantService = app(TenantService::class);
    $tenantService->switchToTenant($tenant);
    
    echo "=== TESTING Setting::getGroup('navbar') ===\n";
    $navbarSettings = Setting::getGroup('navbar');
    
    if ($navbarSettings) {
        echo "✅ Found navbar settings:\n";
        foreach ($navbarSettings as $key => $value) {
            echo "  - {$key}: {$value}\n";
        }
        
        $navbar = $navbarSettings->toArray();
        echo "\n✅ Converted to array:\n";
        print_r($navbar);
        
        echo "\n✅ Brand name from array: " . ($navbar['brand_name'] ?? 'NOT SET') . "\n";
    } else {
        echo "❌ No navbar settings found\n";
    }
    
    // Check if there are any settings at all
    echo "\n=== CHECKING ALL SETTINGS ===\n";
    $allSettings = Setting::all();
    echo "Total settings in tenant DB: " . $allSettings->count() . "\n";
    
    if ($allSettings->count() > 0) {
        echo "Sample settings:\n";
        foreach ($allSettings->take(5) as $setting) {
            echo "  - {$setting->group}.{$setting->key}: {$setting->value}\n";
        }
    }
    
    // Check specifically for brand_name
    echo "\n=== CHECKING BRAND_NAME SPECIFICALLY ===\n";
    $brandNameSetting = Setting::where('group', 'navbar')
                              ->where('key', 'brand_name')
                              ->first();
    
    if ($brandNameSetting) {
        echo "✅ Found brand_name setting: {$brandNameSetting->value}\n";
    } else {
        echo "❌ No brand_name setting found in navbar group\n";
        
        // Check all groups for brand_name
        $allBrandName = Setting::where('key', 'brand_name')->get();
        if ($allBrandName->count() > 0) {
            echo "Found brand_name in other groups:\n";
            foreach ($allBrandName as $setting) {
                echo "  - {$setting->group}.{$setting->key}: {$setting->value}\n";
            }
        }
    }
    
    // Switch back to main
    $tenantService->switchToMain();
    echo "\n✅ Switched back to main database\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
