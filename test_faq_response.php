<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing FAQ Management Response\n";
echo "==============================\n\n";

$url = 'http://127.0.0.1:8000/t/draft/test1/admin/faq?preview=true&t=' . time() . '&website=15';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "URL: $url\n";
echo "HTTP Code: $httpCode\n";
echo "Response Size: " . strlen($response) . " bytes\n\n";

if ($error) {
    echo "❌ Error: $error\n";
} else {
    echo "Response Content:\n";
    echo str_repeat('-', 50) . "\n";
    echo $response;
    echo "\n" . str_repeat('-', 50) . "\n";
}

echo "\nTest completed at " . date('Y-m-d H:i:s') . "\n";
?>
