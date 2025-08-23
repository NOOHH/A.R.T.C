<?php
echo "=== TESTING CUSTOMIZE-WEBSITE API DIRECTLY ===\n\n";

// Include Laravel bootstrap to use the models directly
require_once __DIR__ . '/vendor/autoload.php';

try {
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "Laravel application bootstrapped successfully.\n\n";
    
    // Test the CustomizeWebsiteController behavior directly
    echo "1. Testing CustomizeWebsiteController::current() method...\n";
    
    foreach ([15 => 'test1', 16 => 'test2'] as $websiteId => $slug) {
        echo "\nTesting website $websiteId ($slug):\n";
        
        try {
            // Mock a request with website parameter
            $request = new \Illuminate\Http\Request(['website' => $websiteId]);
            
            // Create controller instance
            $controller = new \App\Http\Controllers\Smartprep\Dashboard\CustomizeWebsiteController(app(\App\Services\TenantService::class));
            
            // Call the current method
            $response = $controller->current($request);
            
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $data = $response->getData(true);
                
                echo "  Response type: JSON\n";
                
                if (isset($data['settings'])) {
                    echo "  Settings found in response:\n";
                    
                    // Look for brand_name
                    $brandName = null;
                    foreach ($data['settings'] as $setting) {
                        if (isset($setting['setting_key']) && $setting['setting_key'] === 'brand_name') {
                            $brandName = $setting['setting_value'];
                            break;
                        }
                    }
                    
                    if ($brandName) {
                        echo "  ✅ Brand name: '$brandName'\n";
                        
                        // Check if it matches expected tenant-specific brand
                        $expectedBrand = "BRAND_TEST" . strtoupper($slug) . "_ISOLATED";
                        if ($brandName === $expectedBrand) {
                            echo "  ✅ Correct tenant-specific brand!\n";
                        } else {
                            echo "  ❌ Wrong brand! Expected: '$expectedBrand'\n";
                        }
                    } else {
                        echo "  ⚠️  No brand_name found in settings\n";
                        echo "  Available settings:\n";
                        foreach ($data['settings'] as $setting) {
                            echo "    - " . ($setting['setting_key'] ?? 'unknown') . ": " . ($setting['setting_value'] ?? 'null') . "\n";
                        }
                    }
                } else {
                    echo "  ⚠️  No settings in response data\n";
                    echo "  Response keys: " . implode(', ', array_keys($data)) . "\n";
                }
            } else {
                echo "  Response type: " . get_class($response) . "\n";
                echo "  Response content: " . substr($response->getContent(), 0, 200) . "...\n";
            }
            
        } catch (Exception $e) {
            echo "  ❌ Error testing website $websiteId: " . $e->getMessage() . "\n";
            echo "  Stack trace: " . $e->getTraceAsString() . "\n";
        }
    }
    
    echo "\n2. Manually testing database connections...\n";
    
    foreach ([15 => 'test1', 16 => 'test2'] as $websiteId => $slug) {
        echo "\nTesting database for website $websiteId ($slug):\n";
        
        try {
            // Find the client by website ID (assuming website ID maps to client ID)
            $client = \App\Models\Client::find($websiteId);
            if (!$client) {
                echo "  ❌ Client $websiteId not found\n";
                continue;
            }
            
            echo "  ✅ Client found: {$client->name}\n";
            echo "  Database: {$client->database_name}\n";
            
            // Switch to tenant database using TenantService
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchTenant($client);
            
            echo "  ✅ Switched to tenant database\n";
            
            // Query ui_settings directly
            $settings = \Illuminate\Support\Facades\DB::connection('tenant')
                ->table('ui_settings')
                ->where('section', 'navbar')
                ->where('setting_key', 'brand_name')
                ->first();
            
            if ($settings) {
                echo "  ✅ Brand name from tenant DB: '{$settings->setting_value}'\n";
            } else {
                echo "  ⚠️  No brand_name found in tenant database\n";
                
                // Check what's in the ui_settings table
                $allSettings = \Illuminate\Support\Facades\DB::connection('tenant')
                    ->table('ui_settings')
                    ->get();
                    
                echo "  UI settings count: " . count($allSettings) . "\n";
                foreach ($allSettings as $setting) {
                    echo "    - {$setting->section}.{$setting->setting_key}: {$setting->setting_value}\n";
                }
            }
            
        } catch (Exception $e) {
            echo "  ❌ Error testing database: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error bootstrapping Laravel: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
