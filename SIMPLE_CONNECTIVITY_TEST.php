<?php
// Simple connectivity test
echo "=== SIMPLE CONNECTIVITY TEST ===\n";

$urls = [
    'Regular Enrollment' => 'http://127.0.0.1:8000/enrollment',
    'Tenant Enrollment' => 'http://127.0.0.1:8000/t/draft/test1/enrollment', 
    'Tenant Homepage' => 'http://127.0.0.1:8000/t/draft/test1'
];

foreach ($urls as $name => $url) {
    echo "Testing $name: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "   HTTP $http_code\n";
}

echo "\n=== TESTING WITH BODY ===\n";

// Test tenant homepage with full response
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/t/draft/test1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "Tenant Homepage Full Test:\n";
echo "HTTP Code: $http_code\n";
if ($error) {
    echo "cURL Error: $error\n";
}
if ($http_code >= 400) {
    echo "Error Response: " . substr($response, 0, 500) . "\n";
} else {
    echo "Response received (" . strlen($response) . " bytes)\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
