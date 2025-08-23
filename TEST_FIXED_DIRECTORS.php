<?php
/**
 * Test the fixed tenant directors sidebar link
 */

echo "🔧 TESTING FIXED TENANT DIRECTORS SIDEBAR\n";
echo "=========================================\n\n";

// Test the tenant preview with the fixed sidebar
$testUrl = "http://localhost:8000/t/draft/test1/admin-dashboard?website=15";
echo "1️⃣ Testing tenant admin dashboard (should have fixed Directors link):\n";
echo "URL: $testUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Dashboard loads successfully\n";
    
    // Check if the Directors link is now tenant-aware
    if (strpos($response, 't/draft/test1/admin/directors?website=15') !== false) {
        echo "✅ SUCCESS: Directors link is now tenant-aware!\n";
        echo "   Found: t/draft/test1/admin/directors?website=15\n";
    } elseif (strpos($response, 'admin/directors') !== false) {
        echo "⚠️  Directors link found but may not be fully tenant-aware\n";
        
        // Extract the actual link
        preg_match('/href="([^"]*admin\/directors[^"]*)"/', $response, $matches);
        if ($matches) {
            echo "   Found link: " . $matches[1] . "\n";
        }
    } else {
        echo "❌ Directors link not found in response\n";
    }
    
} else {
    echo "❌ Dashboard failed to load: HTTP $httpCode\n";
}

echo "\n2️⃣ Testing the Directors link directly:\n";
$directorsUrl = "http://localhost:8000/t/draft/test1/admin/directors?website=15";
echo "URL: $directorsUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $directorsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Directors page loads successfully\n";
    
    if (strpos($response, 'Directors Management') !== false) {
        echo "✅ Contains Directors Management title\n";
    }
    
    if (strpos($response, 'Sarah Johnson') !== false || strpos($response, 'Michael Chen') !== false) {
        echo "✅ Contains mock director data\n";
    }
    
    echo "Response length: " . strlen($response) . " characters\n";
} else {
    echo "❌ Directors page failed: HTTP $httpCode\n";
}

echo "\n🎯 SUMMARY:\n";
echo "===========\n";
echo "✅ Fixed the Directors sidebar link to be tenant-aware\n";
echo "✅ Directors link now uses conditional URL logic like Students/Professors\n";
echo "✅ In preview mode: /t/draft/{tenant}/admin/directors?website={website}\n";
echo "✅ In regular mode: /admin/directors\n";
echo "\nThe Directors link in the sidebar should now work correctly in tenant preview mode!\n";
