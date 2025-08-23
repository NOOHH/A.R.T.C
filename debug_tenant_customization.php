<?php
// Debug tenant customization loading process

echo "🔍 DEBUGGING TENANT CUSTOMIZATION LOADING\n";
echo "=========================================\n\n";

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    // Simulate the loadAdminPreviewCustomization method
    $websiteId = 17; // SmartPrep client ID
    
    echo "1. Looking for client with ID: $websiteId\n";
    
    $client = \App\Models\Client::find($websiteId);
    if ($client) {
        echo "✅ Client found: {$client->name} (slug: {$client->slug})\n";
        
        echo "2. Looking for tenant with slug: {$client->slug}\n";
        $tenantObj = \App\Models\Tenant::where('slug', $client->slug)->first();
        
        if ($tenantObj) {
            echo "✅ Tenant found: {$tenantObj->name}\n";
            
            echo "3. Switching to tenant database...\n";
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToTenant($tenantObj);
            
            echo "✅ Switched to tenant database\n";
            
            echo "4. Loading navbar settings...\n";
            try {
                $navbarSettings = \App\Models\Setting::getGroup('navbar');
                echo "Navbar settings: " . ($navbarSettings ? "Found" : "Not found") . "\n";
                
                if ($navbarSettings) {
                    foreach ($navbarSettings as $key => $value) {
                        echo "  $key: $value\n";
                    }
                }
            } catch (\Exception $e) {
                echo "❌ Error loading navbar settings: " . $e->getMessage() . "\n";
            }
            
            echo "5. Loading admin panel settings...\n";
            try {
                $adminSettings = \App\Models\Setting::getGroup('admin_panel');
                echo "Admin settings: " . ($adminSettings ? "Found" : "Not found") . "\n";
                
                if ($adminSettings) {
                    foreach ($adminSettings as $key => $value) {
                        echo "  $key: $value\n";
                    }
                }
            } catch (\Exception $e) {
                echo "❌ Error loading admin settings: " . $e->getMessage() . "\n";
            }
            
            echo "6. Switching back to main database...\n";
            $tenantService->switchToMain();
            echo "✅ Switched back to main database\n";
            
        } else {
            echo "❌ Tenant not found with slug: {$client->slug}\n";
        }
        
    } else {
        echo "❌ Client not found with ID: $websiteId\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error in tenant customization: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🔍 CHECKING SETTING MODEL\n";
echo "=========================\n";

try {
    // Check if Setting model exists and has getGroup method
    if (class_exists('\App\Models\Setting')) {
        echo "✅ Setting model exists\n";
        
        $reflection = new ReflectionClass('\App\Models\Setting');
        if ($reflection->hasMethod('getGroup')) {
            echo "✅ getGroup method exists\n";
        } else {
            echo "❌ getGroup method does not exist\n";
        }
    } else {
        echo "❌ Setting model does not exist\n";
    }
} catch (\Exception $e) {
    echo "❌ Error checking Setting model: " . $e->getMessage() . "\n";
}
?>
