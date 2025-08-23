<?php

$url = 'http://127.0.0.1:8000/t/draft/test1/professor/profile?preview=true';

echo "Testing Profile Route: $url\n";
echo "=====================================\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$responseSize = strlen($response);

echo "Status Code: $httpCode\n";
echo "Response Length: $responseSize bytes\n\n";

if ($httpCode !== 200) {
    echo "=== ERROR RESPONSE (first 2000 chars) ===\n";
    echo substr($response, 0, 2000) . "\n\n";
    
    // Check for specific error patterns
    if (strpos($response, 'Class') !== false && strpos($response, 'not found') !== false) {
        echo "❌ CLASS NOT FOUND ERROR\n\n";
    }
    if (strpos($response, 'Method') !== false && strpos($response, 'does not exist') !== false) {
        echo "❌ METHOD NOT FOUND ERROR\n\n";
    }
} else {
    echo "✅ SUCCESS\n";
}

curl_close($ch);
