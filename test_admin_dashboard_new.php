<?php

// Test the new admin dashboard route with tenant customization
echo "🔧 Testing Admin Dashboard with Tenant Customization\n";
echo "================================================\n\n";

$testUrls = [
    // Test SmartPrep tenant dashboard
    'http://localhost:8000/t/draft/smartprep/admin-dashboard?website=17',
    'http://localhost:8000/t/draft/smartprep/admin-dashboard',
    
    // Test test1 tenant dashboard  
    'http://localhost:8000/t/draft/test1/admin-dashboard?website=15',
    'http://localhost:8000/t/draft/test1/admin-dashboard',
];

foreach ($testUrls as $url) {
    echo "🧪 Testing: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSize);
    
    curl_close($ch);
    
    echo "   Status: $httpCode\n";
    
    if ($httpCode == 200) {
        // Check for navbar customization
        if (strpos($body, 'SmartPrep') !== false && strpos($url, 'smartprep') !== false) {
            echo "   ✅ SmartPrep branding detected in response!\n";
        } elseif (strpos($body, 'Test Tenant') !== false && strpos($url, 'test1') !== false) {
            echo "   ✅ Test1 tenant branding detected in response!\n";
        } else {
            echo "   ⚠️  Default branding (Ascendo) in response\n";
        }
        
        // Check for dashboard elements
        if (strpos($body, 'total_students') !== false || strpos($body, 'Dashboard') !== false) {
            echo "   ✅ Dashboard content detected\n";
        } else {
            echo "   ❌ Dashboard content NOT detected\n";
        }
    } else {
        echo "   ❌ HTTP Error: $httpCode\n";
        if (strpos($body, 'Error') !== false) {
            preg_match('/<p>Error: ([^<]+)<\/p>/', $body, $matches);
            if (isset($matches[1])) {
                echo "   Error: " . $matches[1] . "\n";
            }
        }
    }
    
    echo "\n";
}

echo "✅ Admin Dashboard Testing Complete!\n";
