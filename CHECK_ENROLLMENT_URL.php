<?php
// Check what enrollment URL is generated on tenant homepage

$tenant_url = "http://localhost:8000/t/draft/artc";

echo "==== CHECKING ENROLLMENT URL ON TENANT HOMEPAGE ====\n\n";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tenant_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "✅ Page loaded successfully\n\n";
    
    // Look for enrollment URLs in the response
    $patterns = [
        'ENROLL NOW href' => '/href="([^"]*enrollment[^"]*)"/',
        'enrollment links' => '/href="([^"]*enrollment[^"]*)"/',
        'tenant enrollment' => '/\/t\/draft\/artc\/enrollment/',
        'regular enrollment' => '/href="[^"]*\/enrollment"/',
    ];
    
    foreach ($patterns as $name => $pattern) {
        if (preg_match_all($pattern, $response, $matches)) {
            echo "✅ Found $name:\n";
            foreach (array_unique($matches[1] ?? $matches[0]) as $match) {
                echo "   - $match\n";
            }
        } else {
            echo "❌ No $name found\n";
        }
        echo "\n";
    }
    
    // Check for ENROLL NOW button specifically
    if (preg_match('/<[^>]*ENROLL NOW[^>]*>.*?href="([^"]*)".*?<[^>]*ENROLL NOW[^>]*>/s', $response, $matches)) {
        echo "✅ ENROLL NOW button URL: {$matches[1]}\n";
    } else if (preg_match('/href="([^"]*)"[^>]*>.*?ENROLL NOW/s', $response, $matches)) {
        echo "✅ ENROLL NOW button URL: {$matches[1]}\n";
    } else {
        echo "❌ Could not find ENROLL NOW button URL\n";
    }
    
} else {
    echo "❌ Failed to load page: HTTP $httpCode\n";
}

echo "\n==== TEST COMPLETE ====\n";
?>
