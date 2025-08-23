<?php
echo "=== TESTING SETTINGS ISOLATION DIRECTLY ===\n\n";

// Include Laravel bootstrap to use the models directly
require_once __DIR__ . '/vendor/autoload.php';

try {
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "Laravel application bootstrapped successfully.\n\n";
    
    // Test the tenant database switching behavior directly
    echo "1. Testing tenant database switching...\n";
    
    foreach ([15 => 'test1', 16 => 'test2'] as $clientId => $slug) {
        echo "\nTesting client $clientId ($slug):\n";
        
        try {
            // Find the client
            $client = \App\Models\Client::find($clientId);
            if (!$client) {
                echo "  ❌ Client $clientId not found\n";
                continue;
            }
            
            echo "  ✅ Client found: {$client->name}\n";
            echo "  Database: {$client->database_name}\n";
            
            // Create a Tenant object (since CustomizeWebsiteController looks for Tenant)
            $tenant = new \App\Models\Tenant();
            $tenant->database_name = $client->database_name;
            
            // Switch to tenant database using TenantService
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToTenant($tenant);
            
            echo "  ✅ Switched to tenant database\n";
            
            // Now test the actual logic from CustomizeWebsiteController
            echo "  Testing UiSetting model (should use main DB)...\n";
            $uiSettings = \App\Models\UiSetting::where('section', 'navbar')
                ->where('setting_key', 'brand_name')
                ->first();
                
            if ($uiSettings) {
                echo "    UiSetting brand_name: '{$uiSettings->setting_value}'\n";
            } else {
                echo "    No UiSetting brand_name found\n";
            }
            
            echo "  Testing Setting model (should use tenant DB)...\n";
            $settingBrand = \App\Models\Setting::getGroup('navbar')['brand_name'] ?? null;
            if ($settingBrand) {
                echo "    Setting brand_name: '$settingBrand'\n";
            } else {
                echo "    No Setting brand_name found\n";
            }
            
            echo "  Testing TenantUiSetting model directly...\n";
            $tenantSettings = \App\Models\TenantUiSetting::where('section', 'navbar')
                ->where('setting_key', 'brand_name')
                ->first();
                
            if ($tenantSettings) {
                echo "    TenantUiSetting brand_name: '{$tenantSettings->setting_value}'\n";
            } else {
                echo "    No TenantUiSetting brand_name found\n";
            }
            
            echo "  Testing direct DB query on tenant connection...\n";
            $directQuery = \Illuminate\Support\Facades\DB::connection('tenant')
                ->table('ui_settings')
                ->where('section', 'navbar')
                ->where('setting_key', 'brand_name')
                ->first();
            
            if ($directQuery) {
                echo "    Direct tenant DB brand_name: '{$directQuery->setting_value}'\n";
            } else {
                echo "    No direct tenant DB brand_name found\n";
            }
            
        } catch (Exception $e) {
            echo "  ❌ Error testing client $clientId: " . $e->getMessage() . "\n";
            echo "     Stack trace: " . $e->getTraceAsString() . "\n";
        }
    }
    
    echo "\n2. Simulating the CustomizeWebsiteController::current() logic...\n";
    
    foreach ([15 => 'test1', 16 => 'test2'] as $clientId => $slug) {
        echo "\nSimulating CustomizeWebsiteController for client $clientId ($slug):\n";
        
        try {
            // Simulate request
            $request = new \Illuminate\Http\Request(['website' => $clientId]);
            $websiteId = $request->get('website');
            
            echo "  Website ID from request: $websiteId\n";
            
            // Find client and switch to tenant (like the controller does)
            $client = \App\Models\Client::find($websiteId);
            if (!$client) {
                echo "  ❌ Client not found\n";
                continue;
            }
            
            echo "  ✅ Client found: {$client->name}\n";
            
            $tenant = new \App\Models\Tenant();
            $tenant->database_name = $client->database_name;
            
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToTenant($tenant);
            
            echo "  ✅ Switched to tenant database: {$client->database_name}\n";
            
            // Default settings from main database (like line 29-39 in controller)
            $settings = [
                'navbar' => [
                    'brand_name' => 'Ascendo Review and Training Center',
                    'brand_logo' => null,
                    'brand_slogan' => 'Smart Review. Smart Results.',
                ],
                // ... other default settings
            ];
            
            echo "  ✅ Loaded default settings\n";
            
            // Load existing settings from main database (like line 41-51 in controller)
            $existingSettings = \App\Models\UiSetting::all();
            echo "  UiSetting count from main DB: " . count($existingSettings) . "\n";
            
            foreach ($existingSettings as $setting) {
                if ($setting->setting_key === 'brand_name') {
                    echo "    UiSetting brand_name: '{$setting->setting_value}'\n";
                    $settings[$setting->section][$setting->setting_key] = $setting->setting_value;
                }
            }
            
            // Now load tenant-specific settings (like line 60-74 in controller)
            echo "  Loading tenant settings...\n";
            
            $tenantSections = ['navbar']; // simplified
            foreach ($tenantSections as $section) {
                try {
                    $sectionSettings = \App\Models\Setting::getGroup($section);
                    echo "    Section '$section' settings count: " . count($sectionSettings) . "\n";
                    
                    if (isset($sectionSettings['brand_name'])) {
                        echo "    Tenant brand_name: '{$sectionSettings['brand_name']}'\n";
                        $settings[$section]['brand_name'] = $sectionSettings['brand_name'];
                    }
                    
                } catch (Exception $e) {
                    echo "    ❌ Error loading section '$section': " . $e->getMessage() . "\n";
                }
            }
            
            // Final brand name
            $finalBrandName = $settings['navbar']['brand_name'] ?? 'DEFAULT';
            echo "  🎯 FINAL BRAND NAME: '$finalBrandName'\n";
            
            // Check if it's the expected tenant-specific brand
            $expectedBrand = "BRAND_TEST" . strtoupper($slug) . "_ISOLATED";
            if ($finalBrandName === $expectedBrand) {
                echo "  ✅ CORRECT! Showing tenant-specific brand\n";
            } else {
                echo "  ❌ WRONG! Expected: '$expectedBrand', Got: '$finalBrandName'\n";
                echo "     🚨 CROSS-CONTAMINATION DETECTED!\n";
            }
            
        } catch (Exception $e) {
            echo "  ❌ Error simulating client $clientId: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error bootstrapping Laravel: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
