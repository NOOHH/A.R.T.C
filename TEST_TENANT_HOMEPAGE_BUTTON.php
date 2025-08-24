<?php
// Test tenant homepage for ENROLL NOW button content
echo "=== TESTING TENANT HOMEPAGE ENROLL NOW BUTTON ===\n";

$tenant_home_url = 'http://127.0.0.1:8000/t/draft/test1';
echo "Testing URL: $tenant_home_url\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tenant_home_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Response Code: $http_code\n\n";

if ($http_code === 200) {
    // Look for the ENROLL NOW button
    if (preg_match('/<a[^>]*class="[^"]*enroll-btn[^"]*"[^>]*href="([^"]*)"[^>]*>.*?ENROLL NOW/i', $response, $matches)) {
        echo "✅ ENROLL NOW button found!\n";
        echo "Button URL: " . $matches[1] . "\n\n";
        
        if (strpos($matches[1], '/t/draft/test1/enrollment') !== false) {
            echo "✅ Button points to tenant enrollment page!\n";
        } else {
            echo "❌ Button does NOT point to tenant enrollment page\n";
            echo "Expected: /t/draft/test1/enrollment\n";
            echo "Actual: " . $matches[1] . "\n";
        }
    } else {
        echo "❌ ENROLL NOW button not found in response\n";
        
        // Look for any enrollment-related links
        if (preg_match_all('/<a[^>]*href="([^"]*enrollment[^"]*)"[^>]*>/i', $response, $all_matches)) {
            echo "\nFound enrollment-related links:\n";
            foreach ($all_matches[1] as $link) {
                echo "- $link\n";
            }
        }
        
        // Look for any buttons with enroll in the class
        if (preg_match_all('/<a[^>]*class="[^"]*enroll[^"]*"[^>]*href="([^"]*)"[^>]*>/i', $response, $btn_matches)) {
            echo "\nFound enroll buttons:\n";
            foreach ($btn_matches[1] as $link) {
                echo "- $link\n";
            }
        }
    }
    
    // Check if tenantSlug variable is being passed
    if (strpos($response, 'data-tenant="test1"') !== false) {
        echo "\n✅ Tenant slug is being passed to the view\n";
    } else {
        echo "\n❌ Tenant slug is NOT being passed to the view\n";
    }
    
} else {
    echo "❌ Failed to load tenant homepage (HTTP $http_code)\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
