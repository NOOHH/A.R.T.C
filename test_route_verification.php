<?php
// Test script to verify the course-content-store route exists and works
echo "=== ROUTE VERIFICATION TEST ===\n";

$baseUrl = 'http://127.0.0.1:8000';

// Test 1: Check if the route exists (should return 422 or 405, not 404)
echo "\n1. Testing route existence...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/admin/modules/course-content-store');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "CURL Error: $error\n";
}

if ($httpCode == 404) {
    echo "❌ ROUTE NOT FOUND - The route doesn't exist\n";
} elseif ($httpCode == 422) {
    echo "✅ ROUTE EXISTS - Returns validation error (expected)\n";
} elseif ($httpCode == 405) {
    echo "✅ ROUTE EXISTS - Method not allowed (GET instead of POST)\n";
} elseif ($httpCode == 419) {
    echo "✅ ROUTE EXISTS - CSRF token required (expected)\n";
} else {
    echo "Route exists but returned code: $httpCode\n";
}

// Test 2: Test CSRF token endpoint
echo "\n2. Testing CSRF token endpoint...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/csrf-token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "CSRF Token Endpoint HTTP Code: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    if (isset($data['csrf_token'])) {
        echo "✅ CSRF Token endpoint working\n";
        echo "Token preview: " . substr($data['csrf_token'], 0, 10) . "...\n";
    } else {
        echo "❌ CSRF Token endpoint not returning token\n";
        echo "Response: $response\n";
    }
} else {
    echo "❌ CSRF Token endpoint failed\n";
    if ($error) echo "Error: $error\n";
}

// Test 3: Test basic HTML file access
echo "\n3. Testing HTML file access...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/test-upload.html');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Test HTML file HTTP Code: $httpCode\n";
if ($httpCode == 200) {
    echo "✅ HTML file accessible\n";
    // Check if the HTML contains the correct action URL
    if (strpos($response, 'action="/admin/modules/course-content-store"') !== false) {
        echo "✅ HTML file contains correct action URL\n";
    } else {
        echo "❌ HTML file does not contain correct action URL\n";
    }
} else {
    echo "❌ HTML file not accessible\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "If route exists but HTML still shows 404, try clearing browser cache\n";
echo "or opening in incognito/private mode.\n";
?>
