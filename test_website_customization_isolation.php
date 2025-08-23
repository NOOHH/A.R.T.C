<?php
require_once 'vendor/autoload.php';

echo "=== TESTING WEBSITE CUSTOMIZATION CROSS-CONTAMINATION ===\n\n";

// Test scenario:
// 1. Customize settings for website 15 (test1)
// 2. Customize settings for website 16 (test2) 
// 3. Check if settings from test1 appear in test2

echo "Setting up test scenario...\n";

// First, let's check what websites exist
echo "Checking available websites...\n";

try {
    // Use Laravel application
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Get websites from database
    $clients = \App\Models\Client::all();
    echo "Found " . count($clients) . " websites:\n";
    foreach ($clients as $client) {
        echo "- ID: {$client->id}, Name: {$client->name}, Slug: {$client->slug}\n";
    }
    
    // Check if we have test1 and test2 or similar
    $testWebsite1 = $clients->where('slug', 'test1')->first();
    $testWebsite2 = $clients->where('slug', 'test2')->first();
    
    if (!$testWebsite1) {
        $testWebsite1 = $clients->first();
        echo "Using first website as test1: {$testWebsite1->slug}\n";
    }
    
    if (!$testWebsite2) {
        $testWebsite2 = $clients->skip(1)->first();
        if ($testWebsite2) {
            echo "Using second website as test2: {$testWebsite2->slug}\n";
        } else {
            echo "Only one website found - cannot test cross-contamination\n";
            exit(1);
        }
    }
    
} catch (Exception $e) {
    echo "Error checking websites: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nTesting cross-contamination between:\n";
echo "Website 1: ID={$testWebsite1->id}, Slug={$testWebsite1->slug}\n";
echo "Website 2: ID={$testWebsite2->id}, Slug={$testWebsite2->slug}\n";

// Create test data
$testValue1 = "TEST_BRAND_" . time() . "_SITE1";
$testValue2 = "TEST_BRAND_" . time() . "_SITE2";

echo "\nTest values:\n";
echo "Site 1 brand: $testValue1\n";
echo "Site 2 brand: $testValue2\n";

echo "\n=== TESTING CUSTOMIZATION ISOLATION ===\n";

// Test 1: Save settings for website 1
echo "1. Saving brand name for website 1...\n";
$result1 = testSaveSettings($testWebsite1->id, $testValue1);
echo "Result: " . ($result1 ? "SUCCESS" : "FAILED") . "\n";

// Test 2: Save settings for website 2
echo "2. Saving brand name for website 2...\n";
$result2 = testSaveSettings($testWebsite2->id, $testValue2);
echo "Result: " . ($result2 ? "SUCCESS" : "FAILED") . "\n";

// Test 3: Check if settings are isolated
echo "3. Checking settings isolation...\n";
$site1Settings = getWebsiteSettings($testWebsite1->id);
$site2Settings = getWebsiteSettings($testWebsite2->id);

echo "Website 1 brand: " . ($site1Settings['brand_name'] ?? 'NOT SET') . "\n";
echo "Website 2 brand: " . ($site2Settings['brand_name'] ?? 'NOT SET') . "\n";

if (($site1Settings['brand_name'] ?? '') === $testValue1 && 
    ($site2Settings['brand_name'] ?? '') === $testValue2) {
    echo "✅ PASSED: Settings are properly isolated\n";
} else {
    echo "❌ FAILED: Settings are being shared between websites!\n";
    echo "Expected: Site1='$testValue1', Site2='$testValue2'\n";
    echo "Actual: Site1='{$site1Settings['brand_name']}', Site2='{$site2Settings['brand_name']}'\n";
}

function testSaveSettings($websiteId, $brandName) {
    $url = "http://127.0.0.1:8000/smartprep/dashboard/settings/navbar/{$websiteId}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'brand_name' => $brandName,
        'navbar_brand_name' => $brandName,
        '_token' => 'test_token'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode >= 200 && $httpCode < 400;
}

function getWebsiteSettings($websiteId) {
    $url = "http://127.0.0.1:8000/smartprep/dashboard/customize-website?website={$websiteId}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Extract settings from response (this is basic - in real scenario you'd parse JSON or HTML)
    $settings = [];
    if (preg_match('/brand_name.*?value="([^"]*)"/', $response, $matches)) {
        $settings['brand_name'] = $matches[1];
    }
    
    return $settings;
}

echo "\n=== TEST COMPLETE ===\n";
?>
