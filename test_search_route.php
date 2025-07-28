<?php
/**
 * Test Search Route Direct
 */

echo "Testing chat search route...\n";

// Simulate a direct test of the search functionality
$url = 'http://127.0.0.1:8000/api/chat/session/search';

// Get CSRF token first
$token_url = 'http://127.0.0.1:8000/sanctum/csrf-cookie';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
$response = curl_exec($ch);
curl_close($ch);

// Extract CSRF token from response
if (preg_match('/XSRF-TOKEN=([^;]+)/', $response, $matches)) {
    $csrf_token = urldecode($matches[1]);
    echo "CSRF Token obtained: " . substr($csrf_token, 0, 20) . "...\n";
} else {
    echo "Could not get CSRF token, using test token\n";
    $csrf_token = 'test-token';
}

// Test the search endpoint
$data = json_encode(['query' => 'vince']);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-CSRF-TOKEN: ' . $csrf_token,
    'X-Requested-With: XMLHttpRequest',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
if ($error) {
    echo "CURL Error: $error\n";
}

if ($httpCode === 200) {
    echo "✅ Search endpoint is working!\n";
    $data = json_decode($response, true);
    if (isset($data['success'])) {
        echo "Response format: " . ($data['success'] ? 'Success' : 'Failed') . "\n";
        if (isset($data['data'])) {
            echo "Users found: " . count($data['data']) . "\n";
        }
    }
} else {
    echo "❌ Search endpoint failed\n";
    echo "Response: $response\n";
}

// Clean up
if (file_exists('cookie.txt')) {
    unlink('cookie.txt');
}

echo "\nDone.\n";
?>
