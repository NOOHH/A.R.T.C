<?php

// Test script to check professor authentication
$baseUrl = 'http://127.0.0.1:8000';

echo "Testing Professor Authentication...\n";

// Test 1: Check if we can access the modules page
echo "\n1. Testing modules page access...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/professor/modules');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($httpCode == 200) {
    echo "✓ Modules page accessible\n";
} else {
    echo "✗ Modules page not accessible (redirected to login)\n";
}

// Test 2: Check AJAX endpoints
echo "\n2. Testing AJAX endpoints...\n";

// Test by-program endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/professor/modules/by-program?program_id=40');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Requested-With: XMLHttpRequest',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "by-program HTTP Code: $httpCode\n";
if ($httpCode == 200) {
    echo "✓ by-program endpoint accessible\n";
} else {
    echo "✗ by-program endpoint not accessible\n";
}

// Test batches endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/professor/modules/batches?program_id=40');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Requested-With: XMLHttpRequest',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "batches HTTP Code: $httpCode\n";
if ($httpCode == 200) {
    echo "✓ batches endpoint accessible\n";
} else {
    echo "✗ batches endpoint not accessible\n";
}

// Test module courses endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/professor/modules/78/courses');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Requested-With: XMLHttpRequest',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "module courses HTTP Code: $httpCode\n";
if ($httpCode == 200) {
    echo "✓ module courses endpoint accessible\n";
} else {
    echo "✗ module courses endpoint not accessible\n";
}

// Test module content endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/professor/modules/78/content');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Requested-With: XMLHttpRequest',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "module content HTTP Code: $httpCode\n";
if ($httpCode == 200) {
    echo "✓ module content endpoint accessible\n";
} else {
    echo "✗ module content endpoint not accessible\n";
}

echo "\nTest completed.\n";
?> 