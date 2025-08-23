<?php
echo "=== TESTING COMPLETE CUSTOMIZATION FLOW ===\n\n";

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

try {
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "Laravel application bootstrapped successfully.\n\n";
    
    // Simulate the complete CustomizeWebsiteController::current() flow
    foreach ([15 => 'test1', 16 => 'test2'] as $clientId => $slug) {
        echo "Testing complete flow for client $clientId ($slug):\n";
        
        try {
            // Step 1: Load default settings from main database (UiSetting)
            echo "  1. Loading default settings from main database...\n";
            
            $settings = [
                'navbar' => \App\Models\UiSetting::getSection('navbar')->toArray(),
            ];
            
            echo "     Main DB brand_name: '" . ($settings['navbar']['brand_name'] ?? 'none') . "'\n";
            
            // Step 2: Find selected website/client (in main database context)
            echo "  2. Finding client in main database...\n";
            
            $selectedWebsite = \App\Models\Client::find($clientId);
            if (!$selectedWebsite) {
                echo "     ❌ Client not found\n";
                continue;
            }
            
            echo "     ✅ Client found: {$selectedWebsite->name} (slug: {$selectedWebsite->slug})\n";
            
            // Step 3: Find corresponding tenant
            echo "  3. Finding tenant record...\n";
            
            $tenant = \App\Models\Tenant::where('slug', $selectedWebsite->slug)->first();
            if (!$tenant) {
                echo "     ❌ Tenant not found\n";
                continue;
            }
            
            echo "     ✅ Tenant found: {$tenant->name} (db: {$tenant->database_name})\n";
            
            // Step 4: Switch to tenant database
            echo "  4. Switching to tenant database...\n";
            
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToTenant($tenant);
            
            echo "     ✅ Switched to tenant database: {$tenant->database_name}\n";
            
            // Step 5: Load tenant-specific settings (Setting model -> TenantUiSetting)
            echo "  5. Loading tenant-specific settings...\n";
            
            try {
                $tenantNavbarSettings = \App\Models\Setting::getGroup('navbar')->toArray();
                echo "     ✅ Loaded tenant navbar settings: " . count($tenantNavbarSettings) . " items\n";
                
                if (isset($tenantNavbarSettings['brand_name'])) {
                    echo "     Tenant brand_name: '{$tenantNavbarSettings['brand_name']}'\n";
                    $settings['navbar']['brand_name'] = $tenantNavbarSettings['brand_name'];
                } else {
                    echo "     No tenant brand_name found\n";
                }
                
            } catch (Exception $e) {
                echo "     ❌ Error loading tenant settings: " . $e->getMessage() . "\n";
            }
            
            // Step 6: Switch back to main database
            echo "  6. Switching back to main database...\n";
            
            $tenantService->switchToMain();
            
            echo "     ✅ Switched back to main database\n";
            
            // Step 7: Final result
            $finalBrandName = $settings['navbar']['brand_name'] ?? 'DEFAULT';
            echo "  🎯 FINAL BRAND NAME: '$finalBrandName'\n";
            
            // Check if it's the expected tenant-specific brand
            $expectedBrand = "BRAND_TEST" . strtoupper($slug) . "_ISOLATED";
            if ($finalBrandName === $expectedBrand) {
                echo "  ✅ SUCCESS! Showing correct tenant-specific brand\n";
            } else {
                echo "  ❌ WRONG! Expected: '$expectedBrand', Got: '$finalBrandName'\n";
                
                if ($finalBrandName === 'ARTC') {
                    echo "     🚨 CROSS-CONTAMINATION DETECTED! Showing main database brand\n";
                } else {
                    echo "     🤔 Unexpected brand value\n";
                }
            }
            
        } catch (Exception $e) {
            echo "  ❌ Error in flow: " . $e->getMessage() . "\n";
            echo "     Stack trace: " . $e->getTraceAsString() . "\n";
        }
        
        echo "\n" . str_repeat("-", 80) . "\n\n";
    }
    
} catch (Exception $e) {
    echo "Error bootstrapping Laravel: " . $e->getMessage() . "\n";
}

echo "=== FLOW TEST COMPLETE ===\n";
?>
