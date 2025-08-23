<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FINAL BRAND NAME VERIFICATION TEST ===\n\n";

try {
    echo "1. Testing admin-dashboard brand text in HTML:\n";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'header' => 'User-Agent: Brand Verification Script'
        ]
    ]);
    
    $response = @file_get_contents('http://127.0.0.1:8000/t/draft/artc/admin-dashboard?website=1', false, $context);
    if ($response) {
        echo "   ✅ Successfully fetched admin dashboard\n";
        
        // Look for the brand text specifically
        if (preg_match('/<span class="brand-text[^>]*>(.*?)<\/span>/i', $response, $matches)) {
            $brandText = trim(strip_tags($matches[1]));
            echo "   📝 Found brand text: '$brandText'\n";
            
            if ($brandText === 'SmartPrep Learning Center') {
                echo "   🎉 SUCCESS! Brand text shows custom name: '$brandText'\n";
            } elseif (strpos($brandText, 'ARTC') !== false) {
                echo "   ❌ FAILED! Still showing ARTC: '$brandText'\n";
            } else {
                echo "   ⚠️  UNKNOWN! Brand text: '$brandText'\n";
            }
        } else {
            echo "   ❌ Could not find brand-text span in response\n";
        }
        
        // Check if ARTC or Admin Portal still exist anywhere
        $artcCount = substr_count(strtolower($response), 'artc');
        $adminPortalCount = substr_count(strtolower($response), 'admin portal');
        
        echo "   📊 Occurrences of 'ARTC': $artcCount\n";
        echo "   📊 Occurrences of 'Admin Portal': $adminPortalCount\n";
        
        // Look for SmartPrep
        $smartprepCount = substr_count(strtolower($response), 'smartprep');
        echo "   📊 Occurrences of 'SmartPrep': $smartprepCount\n";
        
    } else {
        echo "   ❌ ERROR: Could not fetch admin dashboard response\n";
    }
    
    echo "\n2. Testing direct tenant settings:\n";
    
    // Connect to tenant database
    config(['database.connections.tenant.database' => 'smartprep_artc']);
    DB::purge('tenant');
    config(['database.default' => 'tenant']);
    DB::purge('mysql');
    
    // Check brand settings
    $brandNameSetting = DB::table('ui_settings')->where('section', 'navbar')->where('setting_key', 'brand_name')->first();
    if ($brandNameSetting) {
        echo "   ✅ Brand name in DB: '{$brandNameSetting->setting_value}'\n";
    } else {
        echo "   ❌ Brand name setting not found in DB\n";
    }
    
    echo "\n3. Testing AdminPreviewCustomization loadAdminPreviewCustomization:\n";
    
    // Test the trait method
    $websiteId = 1;
    $_GET['website'] = $websiteId; // Simulate request parameter
    
    $client = \App\Models\Client::first();
    if ($client) {
        $tenant = \App\Models\Tenant::where('slug', $client->slug)->first();
        if ($tenant) {
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToTenant($tenant);
            
            $navbarSettings = \App\Models\Setting::getGroup('navbar');
            $finalBrandName = $navbarSettings ? $navbarSettings->get('brand_name', 'Ascendo Review and Training Center') : 'Ascendo Review and Training Center';
            
            echo "   ✅ Trait would return brand_name: '$finalBrandName'\n";
            
            $tenantService->switchToMain();
        }
    }
    
    echo "\n4. Testing all admin preview pages:\n";
    
    $previewPages = [
        'dashboard' => 'http://127.0.0.1:8000/t/draft/artc/admin-dashboard?website=1',
        'faq' => 'http://127.0.0.1:8000/t/draft/artc/admin/faq?website=1',
        'announcements' => 'http://127.0.0.1:8000/t/draft/artc/admin/announcements?website=1'
    ];
    
    foreach ($previewPages as $pageName => $url) {
        $pageResponse = @file_get_contents($url, false, $context);
        if ($pageResponse && preg_match('/<span class="brand-text[^>]*>(.*?)<\/span>/i', $pageResponse, $matches)) {
            $pageBrandText = trim(strip_tags($matches[1]));
            echo "   📄 $pageName page brand text: '$pageBrandText'\n";
        } else {
            echo "   ❌ Could not get brand text from $pageName page\n";
        }
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
