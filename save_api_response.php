<?php

echo "Testing API and saving full response...\n";

$url = "http://127.0.0.1:8000/smartprep/api/ui-settings?website=10";
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response length: " . strlen($response) . "\n";

// Save response to file for analysis
file_put_contents('api_response.html', $response);
echo "Response saved to api_response.html\n";

echo "Done.\n";
