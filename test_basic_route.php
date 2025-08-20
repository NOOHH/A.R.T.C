<?php

echo "Testing basic endpoint...\n";

// Test a basic route
$url = "http://127.0.0.1:8000/";
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}

echo "Response length: " . strlen($response) . "\n";
echo "First 200 chars: " . substr($response, 0, 200) . "\n";

echo "Done.\n";
