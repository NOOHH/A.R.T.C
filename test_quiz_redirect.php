<?php

// Test script to check if quiz generator route is working
$url = 'http://127.0.0.1:8000/admin/quiz-generator';

echo "Testing redirect to: $url\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Final URL: $redirectUrl\n";
echo "Response headers:\n";
echo substr($response, 0, 500) . "\n";

?>
