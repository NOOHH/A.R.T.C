<?php
// Quick test to check what payment routes are returning
$url = 'http://localhost:8000/t/draft/test1/admin/payments/pending?website=15&preview=true';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Payment Pending Response ({$httpCode}):\n";
echo "Length: " . strlen($response) . " bytes\n";
echo "Content:\n";
echo $response;
echo "\n\n" . str_repeat('=', 50) . "\n";

$url2 = 'http://localhost:8000/t/draft/test1/admin/payments/history?website=15&preview=true';
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $url2);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

echo "Payment History Response ({$httpCode2}):\n";
echo "Length: " . strlen($response2) . " bytes\n";
echo "Content:\n";
echo $response2;
?>
