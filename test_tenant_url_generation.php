<?php
echo "🔍 TESTING TENANT-AWARE URL GENERATION\n";
echo "=====================================\n\n";

// Simulate different URL contexts
$testContexts = [
    'Regular site' => 'http://127.0.0.1:8000/enrollment',
    'Tenant site' => 'http://127.0.0.1:8000/t/draft/artc/enrollment'
];

foreach ($testContexts as $context => $url) {
    echo "Testing context: $context\n";
    echo "Current URL: $url\n";
    
    // Test the helper function logic
    if (preg_match('/\/t\/(?:draft\/)?([^\/]+)/', $url, $matches)) {
        $tenant = $matches[1];
        $isDraft = strpos($url, '/draft/') !== false;
        
        if ($isDraft) {
            $modularUrl = "http://127.0.0.1:8000/t/draft/{$tenant}/enrollment/modular";
            $fullUrl = "http://127.0.0.1:8000/t/draft/{$tenant}/enrollment/full";
        } else {
            $modularUrl = "http://127.0.0.1:8000/t/{$tenant}/enrollment/modular";
            $fullUrl = "http://127.0.0.1:8000/t/{$tenant}/enrollment/full";
        }
        
        echo "✅ Tenant detected: $tenant" . ($isDraft ? " (draft)" : "") . "\n";
        echo "🔗 Modular URL: $modularUrl\n";
        echo "🔗 Full URL: $fullUrl\n";
    } else {
        echo "ℹ️  Non-tenant context - would use regular routes\n";
        echo "🔗 Modular URL: http://127.0.0.1:8000/enrollment/modular\n";
        echo "🔗 Full URL: http://127.0.0.1:8000/enrollment/full\n";
    }
    echo "\n";
}

echo "🧪 Now testing the actual enrollment page...\n\n";

// Test that the tenant enrollment page loads and check what URLs it generates
$tenantUrl = 'http://127.0.0.1:8000/t/draft/artc/enrollment';
echo "📋 Fetching: $tenantUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tenantUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";

if ($httpCode == 200 && $response) {
    // Look for the modular enrollment button in the response
    if (preg_match('/href="([^"]+)"[^>]*id="modular-enroll-btn"/', $response, $matches)) {
        $buttonUrl = $matches[1];
        echo "🎯 Found modular button URL: $buttonUrl\n";
        
        if (strpos($buttonUrl, '/t/draft/artc/enrollment/modular') !== false) {
            echo "✅ SUCCESS! Button now uses tenant-aware URL\n";
        } else {
            echo "❌ STILL WRONG! Button still uses: $buttonUrl\n";
        }
    } else {
        echo "⚠️  Could not find modular button in response\n";
    }
    
    // Also check for the full enrollment button
    if (preg_match('/href="([^"]+)"[^>]*class="[^"]*enrollment-btn[^"]*"/', $response, $matches)) {
        $fullButtonUrl = $matches[1];
        echo "🎯 Found full button URL: $fullButtonUrl\n";
        
        if (strpos($fullButtonUrl, '/t/draft/artc/enrollment/full') !== false) {
            echo "✅ SUCCESS! Full button also uses tenant-aware URL\n";
        } else {
            echo "❌ Full button issue: $fullButtonUrl\n";
        }
    }
} else {
    echo "❌ Failed to fetch enrollment page: HTTP $httpCode\n";
}

echo "\n=== TENANT URL GENERATION TEST COMPLETE ===\n";
?>
