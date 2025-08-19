<?php
// Simple test to check if admin dashboard is being called correctly
echo "Testing admin dashboard route...\n";

// Test via HTTP
$url = "http://localhost:8000/admin-dashboard?preview=true";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'follow_location' => false
    ]
]);

$response = @file_get_contents($url, false, $context);
$headers = isset($http_response_header) ? $http_response_header : [];

echo "Response headers:\n";
foreach ($headers as $header) {
    echo "  $header\n";
}

echo "\nFirst 500 characters of response:\n";
echo substr($response ?: 'No response', 0, 500) . "\n";
