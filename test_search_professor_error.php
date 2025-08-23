<?php
require_once 'vendor/autoload.php';

echo "=== Testing Search Professor Database Error ===\n\n";

// Test the search endpoint that directly queries professors table
$searchUrl = 'http://127.0.0.1:8000/search-now?query=test&type=professors';

echo "Testing search endpoint: $searchUrl\n";

// Use curl to test the endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $searchUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\nHTTP Status Code: $httpCode\n";
echo "Response:\n";
echo str_repeat("=", 50) . "\n";
echo $response;
echo "\n" . str_repeat("=", 50) . "\n";

// Also test with preview mode to see the difference
echo "\n\nTesting with preview mode:\n";
$previewUrl = 'http://127.0.0.1:8000/search-now?query=test&type=professors&preview=true';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $previewUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HEADER, true);

$previewResponse = curl_exec($ch);
$previewHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Preview HTTP Status Code: $previewHttpCode\n";
echo "Preview Response:\n";
echo str_repeat("=", 50) . "\n";
echo $previewResponse;
echo "\n" . str_repeat("=", 50) . "\n";

echo "\n=== Test Complete ===\n";
?>
