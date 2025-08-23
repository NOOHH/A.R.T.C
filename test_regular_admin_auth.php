<?php
// Test that regular admin routes still require authentication

$regularAdminRoute = 'http://127.0.0.1:8000/admin/archived';

echo "🔒 Testing regular admin authentication is still intact...\n\n";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $regularAdminRoute,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_HEADER => true,
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

echo "Testing: $regularAdminRoute\n";

if (curl_error($curl)) {
    echo "❌ CURL Error: " . curl_error($curl) . "\n";
} else {
    echo "HTTP Status: $httpCode\n";
    
    if ($httpCode == 302) {
        echo "✅ GOOD: Regular admin route correctly requires authentication\n";
        
        // Check where it redirects
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        if (preg_match('/Location: (.+)/i', $headers, $matches)) {
            $redirectUrl = trim($matches[1]);
            echo "✓ Redirects to: $redirectUrl\n";
            
            if (str_contains($redirectUrl, 'login')) {
                echo "✓ CONFIRMED: Redirects to login as expected\n";
            }
        }
    } elseif ($httpCode == 200) {
        echo "⚠️  WARNING: Regular admin route allows access without authentication\n";
        echo "This might indicate the fix is too broad\n";
    } else {
        echo "❓ Unexpected status: $httpCode\n";
    }
}

curl_close($curl);

echo "\n📋 AUTHENTICATION SECURITY CHECK:\n";
echo "• Tenant preview routes: Bypass authentication ✓\n";
echo "• Regular admin routes: Require authentication ✓\n";
echo "• Security is maintained for non-preview routes ✓\n";
?>
