<?php
echo "=== TESTING API ENDPOINTS ===\n\n";

echo "Testing /api/programs endpoint...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/programs');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: " . substr($response, 0, 500) . "\n\n";

if ($httpCode === 200) {
    echo "✅ API endpoint working\n";
    $data = json_decode($response, true);
    if ($data && is_array($data)) {
        echo "Programs count: " . count($data) . "\n";
    }
} else {
    echo "❌ API endpoint failed\n";
}

echo "\nTesting homepage...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";

if ($httpCode === 200) {
    echo "✅ Homepage working\n";
    if (strpos($response, 'fetch') !== false) {
        echo "Page contains JavaScript fetch calls\n";
    }
} else {
    echo "❌ Homepage failed\n";
    echo "Response: " . substr($response, 0, 500) . "\n";
}
?>
