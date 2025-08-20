<?php

echo "Testing tenant URL with website parameter...\n";

// Test tenant URL with website parameter
$tenantUrl = "http://127.0.0.1:8000/t/client?website=10&preview=true";
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $tenantUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "URL: $tenantUrl\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Response length: " . strlen($response) . "\n";
    
    // Check if the response contains the updated brand name
    if (strpos($response, 'DEBUG_TEST_232023') !== false) {
        echo "✅ SUCCESS: Found tenant-specific brand name 'DEBUG_TEST_232023' in tenant page!\n";
    } elseif (strpos($response, 'client') !== false) {
        echo "❌ ISSUE: Still showing default brand name 'client'\n";
    } else {
        echo "⚠️  Brand name not found in response\n";
    }
    
    // Check for any navigation elements
    if (strpos($response, 'navbar') !== false || strpos($response, 'nav-') !== false) {
        echo "✅ Found navbar elements in response\n";
    } else {
        echo "⚠️  No navbar elements found\n";
    }
}

echo "Done.\n";
